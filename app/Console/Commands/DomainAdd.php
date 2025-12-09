<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;
use Exception;

class DomainAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:add {name?} {--status=active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new domain to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domainName = $this->argument('name');
        
        if (!$domainName) {
            $domainName = $this->ask('Enter the domain name (e.g., example.com)');
        }

        if (!$domainName) {
            $this->error('Domain name is required.');
            return 1;
        }

        // Validate domain name format
        if (!preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/', $domainName)) {
            $this->error('Invalid domain name format.');
            return 1;
        }

        // Check if domain already exists
        if (Domain::where('name', $domainName)->exists()) {
            $this->warn("Domain '{$domainName}' already exists in the database.");
            
            if (!$this->confirm('Do you want to view its details?', true)) {
                return 0;
            }
            
            $domain = Domain::where('name', $domainName)->first();
            $this->displayDomainInfo($domain);
            return 0;
        }

        try {
            $tld = $this->extractTld($domainName);
            $status = $this->option('status');
            
            $domain = Domain::create([
                'name' => $domainName,
                'tld' => $tld,
                'status' => $status,
            ]);

            $this->info("✓ Successfully added domain: {$domainName}");
            $this->info("  ID: {$domain->id}");
            $this->info("  TLD: {$domain->tld}");
            $this->info("  Status: {$domain->status}");
            
            if ($this->confirm('Would you like to perform a WHOIS lookup now?', true)) {
                $this->call('whois:lookup', ['domain' => $domainName]);
            }
            
            if ($this->confirm('Would you like to perform a DNS lookup now?', true)) {
                $this->call('dns:lookup', ['domain' => $domainName]);
            }

            return 0;
            
        } catch (Exception $e) {
            $this->error("Failed to add domain: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Extract TLD from domain name
     */
    private function extractTld(string $domain): string
    {
        $parts = explode('.', $domain);
        return end($parts);
    }

    /**
     * Display domain information
     */
    private function displayDomainInfo(Domain $domain): void
    {
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $domain->id],
                ['Name', $domain->name],
                ['TLD', $domain->tld],
                ['Registrar', $domain->registrar ?? 'N/A'],
                ['Status', $domain->status],
                ['Registered At', $domain->registered_at?->format('Y-m-d') ?? 'N/A'],
                ['Expires At', $domain->expires_at?->format('Y-m-d') ?? 'N/A'],
                ['WHOIS Records', $domain->whoisRecords()->count()],
                ['DNS Records', $domain->dnsRecords()->count()],
                ['Created At', $domain->created_at->format('Y-m-d H:i:s')],
            ]
        );
    }
}
