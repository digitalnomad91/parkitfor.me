<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\WebScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class DomainController extends Controller
{
    public function create()
    {
        return Inertia::render('Domains/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:domains,name', 'regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/'],
            'status' => ['nullable', 'string', 'in:active,inactive,pending'],
        ]);

        $tld = $this->extractTld($validated['name']);
        
        $domain = Domain::create([
            'name' => $validated['name'],
            'tld' => $tld,
            'status' => $validated['status'] ?? 'active',
        ]);

        // Optionally perform WHOIS and DNS lookups
        if ($request->has('perform_whois')) {
            Artisan::call('whois:lookup', ['domain' => $domain->name]);
        }

        if ($request->has('perform_dns')) {
            Artisan::call('dns:lookup', ['domain' => $domain->name]);
        }

        return redirect()->route('domains')
            ->with('success', "Domain '{$domain->name}' added successfully!");
    }

    public function show(Domain $domain)
    {
        $domain->loadCount(['whoisRecords', 'dnsRecords', 'scrapes']);

        $latestWhois = $domain->whoisRecords()->latest()->first();
        $recentWhoisRecords = $domain->whoisRecords()
            ->latest()
            ->limit(5)
            ->get();

        $dnsRecords = $domain->dnsRecords()
            ->latest()
            ->get();

        $recentScrapes = $domain->scrapes()
            ->withCount(['assets', 'links'])
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Domains/Show', [
            'domain' => $domain,
            'latestWhois' => $latestWhois,
            'recentWhoisRecords' => $recentWhoisRecords,
            'dnsRecords' => $dnsRecords,
            'recentScrapes' => $recentScrapes,
        ]);
    }

    public function whoisRecords(Request $request, Domain $domain)
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'id',
            'registrar',
            'creation_date',
            'expiration_date',
            'updated_date',
            'status',
            'created_at',
        ];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $whoisRecords = $domain->whoisRecords()
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Domains/WhoisRecords', [
            'domain' => $domain,
            'whoisRecords' => $whoisRecords,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function dnsRecords(Domain $domain)
    {
        $dnsRecords = $domain->dnsRecords()
            ->latest()
            ->paginate(20);

        return Inertia::render('Domains/DnsRecords', [
            'domain' => $domain,
            'dnsRecords' => $dnsRecords,
        ]);
    }

    public function performDnsLookup(Domain $domain)
    {
        Artisan::call('dns:lookup', ['domain' => $domain->name]);

        return redirect()->back()
            ->with('success', "DNS lookup completed for '{$domain->name}'!");
    }

    public function scrapes(Request $request, Domain $domain)
    {
        $sort = $request->get('sort', 'scraped_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'id',
            'url',
            'title',
            'status',
            'http_status_code',
            'assets_count',
            'links_count',
            'scraped_at',
            'created_at',
        ];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'scraped_at';
        }

        $scrapes = $domain->scrapes()
            ->withCount(['assets', 'links'])
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Domains/Scrapes', [
            'domain' => $domain,
            'scrapes' => $scrapes,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function scrapeShow(Domain $domain, $scrapeId)
    {
        $scrape = $domain->scrapes()->with(['assets', 'links'])->findOrFail($scrapeId);

        return Inertia::render('Domains/ScrapeDetail', [
            'domain' => $domain,
            'scrape' => $scrape,
        ]);
    }

    public function performScrape(Domain $domain, WebScraperService $scraper)
    {
        try {
            $scrape = $scraper->scrapeDomain($domain);
            
            return redirect()->route('domains.scrapes', $domain->id)
                ->with('success', "Website scraped successfully! Captured {$scrape->assets()->count()} assets and {$scrape->links()->count()} links.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', "Failed to scrape website: {$e->getMessage()}");
        }
    }

    private function extractTld(string $domain): string
    {
        $parts = explode('.', $domain);
        return end($parts);
    }
}
