<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\DnsRecord;
use Illuminate\Console\Command;
use Exception;

class DnsLookup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dns:lookup {domain?} {--all} {--types=A,AAAA,MX,NS,CNAME,TXT}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform DNS lookup for a domain or all domains';

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

            $this->info("Performing DNS lookup for {$domains->count()} domain(s)...");
            
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
     * Perform DNS lookup for a domain
     */
    private function performLookup(Domain $domain): void
    {
        $this->info("Looking up DNS records for: {$domain->name}");

        $types = explode(',', $this->option('types'));
        $recordsFound = 0;

        foreach ($types as $type) {
            $type = trim(strtoupper($type));
            
            try {
                $records = $this->queryDns($domain->name, $type);
                
                foreach ($records as $record) {
                    DnsRecord::create([
                        'domain_id' => $domain->id,
                        'record_type' => $type,
                        'name' => $record['name'] ?? $domain->name,
                        'value' => $record['value'],
                        'ttl' => $record['ttl'] ?? null,
                        'priority' => $record['priority'] ?? null,
                        'raw_data' => $record,
                    ]);
                    $recordsFound++;
                }
                
                if (!empty($records)) {
                    $this->line("  ✓ {$type}: Found " . count($records) . " record(s)");
                }
                
            } catch (Exception $e) {
                $this->line("  ✗ {$type}: {$e->getMessage()}");
            }
        }

        if ($recordsFound > 0) {
            $this->info("✓ DNS lookup completed for {$domain->name} - {$recordsFound} records stored");
        } else {
            $this->warn("⚠ No DNS records found for {$domain->name}");
        }
    }

    /**
     * Query DNS records
     */
    private function queryDns(string $domain, string $type): array
    {
        $typeMap = [
            'A' => DNS_A,
            'AAAA' => DNS_AAAA,
            'MX' => DNS_MX,
            'NS' => DNS_NS,
            'CNAME' => DNS_CNAME,
            'TXT' => DNS_TXT,
            'SOA' => DNS_SOA,
            'PTR' => DNS_PTR,
            'SRV' => DNS_SRV,
        ];

        if (!isset($typeMap[$type])) {
            throw new Exception("Unsupported DNS record type: {$type}");
        }

        $dnsType = $typeMap[$type];
        $records = @dns_get_record($domain, $dnsType);

        if ($records === false) {
            throw new Exception("Failed to query DNS records");
        }

        return $this->formatDnsRecords($records, $type);
    }

    /**
     * Format DNS records
     */
    private function formatDnsRecords(array $records, string $type): array
    {
        $formatted = [];

        foreach ($records as $record) {
            $entry = [
                'name' => $record['host'] ?? '',
                'ttl' => $record['ttl'] ?? null,
            ];

            switch ($type) {
                case 'A':
                case 'AAAA':
                    $entry['value'] = $record['ip'] ?? $record['ipv6'] ?? '';
                    break;

                case 'MX':
                    $entry['value'] = $record['target'] ?? '';
                    $entry['priority'] = $record['pri'] ?? null;
                    break;

                case 'NS':
                    $entry['value'] = $record['target'] ?? '';
                    break;

                case 'CNAME':
                    $entry['value'] = $record['target'] ?? '';
                    break;

                case 'TXT':
                    $entry['value'] = $record['txt'] ?? '';
                    break;

                case 'SOA':
                    $entry['value'] = json_encode([
                        'mname' => $record['mname'] ?? '',
                        'rname' => $record['rname'] ?? '',
                        'serial' => $record['serial'] ?? '',
                    ]);
                    break;

                default:
                    $entry['value'] = json_encode($record);
            }

            if (!empty($entry['value'])) {
                $formatted[] = $entry;
            }
        }

        return $formatted;
    }

    /**
     * Extract TLD from domain name
     */
    private function extractTld(string $domain): string
    {
        $parts = explode('.', $domain);
        return end($parts);
    }
}
