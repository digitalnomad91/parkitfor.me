<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\WhoisRecord;
use Illuminate\Console\Command;
use Exception;

class WhoisLookup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whois:lookup {domain?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform WHOIS lookup for a domain or all domains';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $domains = Domain::all();
            
            if ($domains->isEmpty()) {
                $this->error('No domains found in the database.');
                return 1;
            }

            $this->info("Performing WHOIS lookup for {$domains->count()} domain(s)...");
            
            foreach ($domains as $domain) {
                $this->performLookup($domain);
            }
            
            return 0;
        }

        $domainName = $this->argument('domain');
        
        if (!$domainName) {
            $domainName = $this->ask('Enter the domain name to lookup');
        }

        if (!$domainName) {
            $this->error('Domain name is required.');
            return 1;
        }

        // Find or create the domain
        $domain = Domain::firstOrCreate(
            ['name' => $domainName],
            ['tld' => $this->extractTld($domainName)]
        );

        $this->performLookup($domain);

        return 0;
    }

    /**
     * Perform WHOIS lookup for a domain
     */
    private function performLookup(Domain $domain): void
    {
        $this->info("Looking up WHOIS data for: {$domain->name}");

        try {
            $whoisData = $this->queryWhois($domain->name);
            
            // Parse the WHOIS data
            $parsedData = $this->parseWhoisData($whoisData);
            
            // Store the record
            WhoisRecord::create([
                'domain_id' => $domain->id,
                'raw_whois_data' => $whoisData,
                'registrar' => $parsedData['registrar'] ?? null,
                'creation_date' => $parsedData['creation_date'] ?? null,
                'expiration_date' => $parsedData['expiration_date'] ?? null,
                'updated_date' => $parsedData['updated_date'] ?? null,
                'nameservers' => $parsedData['nameservers'] ?? null,
                'status' => $parsedData['status'] ?? null,
                'registrant_name' => $parsedData['registrant_name'] ?? null,
                'registrant_email' => $parsedData['registrant_email'] ?? null,
                'registrant_organization' => $parsedData['registrant_organization'] ?? null,
            ]);

            $this->info("✓ WHOIS lookup completed for {$domain->name}");
            
        } catch (Exception $e) {
            $this->error("✗ Failed to lookup {$domain->name}: {$e->getMessage()}");
        }
    }

    /**
     * Query WHOIS server
     */
    private function queryWhois(string $domain): string
    {
        $tld = $this->extractTld($domain);
        $whoisServer = $this->getWhoisServer($tld);

        $fp = fsockopen($whoisServer, 43, $errno, $errstr, 10);
        
        if (!$fp) {
            throw new Exception("Cannot connect to WHOIS server: {$whoisServer} (Error: {$errstr})");
        }

        fputs($fp, $domain . "\r\n");
        
        $response = '';
        while (!feof($fp)) {
            $response .= fgets($fp, 128);
        }
        
        fclose($fp);

        if (empty($response)) {
            throw new Exception("Empty response from WHOIS server");
        }

        return $response;
    }

    /**
     * Parse WHOIS data
     */
    private function parseWhoisData(string $whoisData): array
    {
        $data = [];
        
        // Parse registrar
        if (preg_match('/Registrar:\s*(.+)/i', $whoisData, $matches)) {
            $data['registrar'] = trim($matches[1]);
        }
        
        // Parse creation date
        if (preg_match('/Creation Date:\s*(.+)/i', $whoisData, $matches) ||
            preg_match('/Created:\s*(.+)/i', $whoisData, $matches) ||
            preg_match('/Created On:\s*(.+)/i', $whoisData, $matches)) {
            $data['creation_date'] = $this->parseDate(trim($matches[1]));
        }
        
        // Parse expiration date
        if (preg_match('/Expir(?:y|ation) Date:\s*(.+)/i', $whoisData, $matches) ||
            preg_match('/Expires:\s*(.+)/i', $whoisData, $matches) ||
            preg_match('/Expires On:\s*(.+)/i', $whoisData, $matches)) {
            $data['expiration_date'] = $this->parseDate(trim($matches[1]));
        }
        
        // Parse updated date
        if (preg_match('/Updated Date:\s*(.+)/i', $whoisData, $matches) ||
            preg_match('/Last Updated:\s*(.+)/i', $whoisData, $matches)) {
            $data['updated_date'] = $this->parseDate(trim($matches[1]));
        }
        
        // Parse nameservers
        preg_match_all('/Name Server:\s*(.+)/i', $whoisData, $matches);
        if (!empty($matches[1])) {
            $data['nameservers'] = array_map('trim', array_map('strtolower', $matches[1]));
        }
        
        // Parse status
        if (preg_match('/Domain Status:\s*(.+)/i', $whoisData, $matches) ||
            preg_match('/Status:\s*(.+)/i', $whoisData, $matches)) {
            $data['status'] = trim($matches[1]);
        }
        
        // Parse registrant info
        if (preg_match('/Registrant Name:\s*(.+)/i', $whoisData, $matches)) {
            $data['registrant_name'] = trim($matches[1]);
        }
        
        if (preg_match('/Registrant Email:\s*(.+)/i', $whoisData, $matches)) {
            $data['registrant_email'] = trim($matches[1]);
        }
        
        if (preg_match('/Registrant Organization:\s*(.+)/i', $whoisData, $matches)) {
            $data['registrant_organization'] = trim($matches[1]);
        }
        
        return $data;
    }

    /**
     * Parse date string
     */
    private function parseDate(string $dateString): ?string
    {
        try {
            $timestamp = strtotime($dateString);
            return $timestamp ? date('Y-m-d', $timestamp) : null;
        } catch (Exception $e) {
            return null;
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
     * Get WHOIS server for TLD
     */
    private function getWhoisServer(string $tld): string
    {
        $servers = [
            'com' => 'whois.verisign-grs.com',
            'net' => 'whois.verisign-grs.com',
            'org' => 'whois.pir.org',
            'info' => 'whois.afilias.net',
            'biz' => 'whois.biz',
            'us' => 'whois.nic.us',
            'uk' => 'whois.nic.uk',
            'co' => 'whois.nic.co',
            'io' => 'whois.nic.io',
            'de' => 'whois.denic.de',
            'fr' => 'whois.nic.fr',
            'it' => 'whois.nic.it',
            'nl' => 'whois.domain-registry.nl',
            'eu' => 'whois.eu',
            'ca' => 'whois.cira.ca',
            'au' => 'whois.auda.org.au',
            'cn' => 'whois.cnnic.cn',
            'jp' => 'whois.jprs.jp',
            'in' => 'whois.registry.in',
            'ru' => 'whois.tcinet.ru',
            'br' => 'whois.registro.br',
        ];

        return $servers[strtolower($tld)] ?? 'whois.iana.org';
    }
}
