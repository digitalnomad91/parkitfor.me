<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Services\WebScraperService;
use Illuminate\Console\Command;
use Exception;

class ScrapeWebsite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:website {domain?} {--url=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape a website to capture HTML, assets, screenshots, and links';

    /**
     * Execute the console command.
     */
    public function handle(WebScraperService $scraper)
    {
        if ($this->option('all')) {
            $domains = Domain::all();
            
            if ($domains->isEmpty()) {
                $this->error('No domains found in the database.');
                return 1;
            }

            $this->info("Scraping {$domains->count()} domain(s)...");
            
            foreach ($domains as $domain) {
                $this->scrapeDomain($scraper, $domain);
            }
            
            return 0;
        }

        $domainName = $this->argument('domain');
        
        if (!$domainName) {
            $domainName = $this->ask('Enter the domain name to scrape');
        }

        if (!$domainName) {
            $this->error('Domain name is required.');
            return 1;
        }

        // Find or create the domain
        $domain = Domain::where('name', $domainName)->first();
        
        if (!$domain) {
            $this->error("Domain '{$domainName}' not found. Please add it first using domain:add");
            return 1;
        }

        $this->scrapeDomain($scraper, $domain, $this->option('url'));

        return 0;
    }

    /**
     * Scrape a domain
     */
    protected function scrapeDomain(WebScraperService $scraper, Domain $domain, ?string $url = null): void
    {
        $this->info("Scraping: {$domain->name}");

        try {
            $scrape = $scraper->scrapeDomain($domain, $url);
            
            $this->info("✓ Scrape completed!");
            $this->line("  Status: {$scrape->status}");
            $this->line("  Title: {$scrape->title}");
            $this->line("  HTTP Status: {$scrape->http_status_code}");
            $this->line("  Assets: {$scrape->assets()->count()}");
            $this->line("  Links: {$scrape->links()->count()}");
            
            if ($scrape->screenshot_path) {
                $this->line("  Screenshot: {$scrape->screenshot_path}");
            }
            
        } catch (Exception $e) {
            $this->error("✗ Failed to scrape {$domain->name}: {$e->getMessage()}");
        }
    }
}

