<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\WebScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DomainController extends Controller
{
    public function create()
    {
        return view('domains.create');
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

    public function dnsRecords($id)
    {
        $domain = Domain::with('dnsRecords')->findOrFail($id);
        
        $dnsRecords = $domain->dnsRecords()
            ->latest()
            ->paginate(20);

        return view('domains.dns-records', compact('domain', 'dnsRecords'));
    }

    public function performDnsLookup($id)
    {
        $domain = Domain::findOrFail($id);
        
        Artisan::call('dns:lookup', ['domain' => $domain->name]);

        return redirect()->back()
            ->with('success', "DNS lookup completed for '{$domain->name}'!");
    }

    public function scrapes($id)
    {
        $domain = Domain::with('scrapes')->findOrFail($id);
        
        $scrapes = $domain->scrapes()
            ->latest()
            ->paginate(10);

        return view('domains.scrapes', compact('domain', 'scrapes'));
    }

    public function scrapeShow($domainId, $scrapeId)
    {
        $domain = Domain::findOrFail($domainId);
        $scrape = $domain->scrapes()->with(['assets', 'links'])->findOrFail($scrapeId);

        return view('domains.scrape-detail', compact('domain', 'scrape'));
    }

    public function performScrape($id, WebScraperService $scraper)
    {
        $domain = Domain::findOrFail($id);
        
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

