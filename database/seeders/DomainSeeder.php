<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\WhoisRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domains = [
            [
                'name' => 'example.com',
                'tld' => 'com',
                'registrar' => 'Example Registrar Inc.',
                'status' => 'active',
                'registered_at' => Carbon::parse('2020-01-15'),
                'expires_at' => Carbon::parse('2026-01-15'),
            ],
            [
                'name' => 'test-domain.net',
                'tld' => 'net',
                'registrar' => 'Network Solutions',
                'status' => 'active',
                'registered_at' => Carbon::parse('2019-05-20'),
                'expires_at' => Carbon::parse('2025-05-20'),
            ],
            [
                'name' => 'sample.org',
                'tld' => 'org',
                'registrar' => 'Public Interest Registry',
                'status' => 'active',
                'registered_at' => Carbon::parse('2021-03-10'),
                'expires_at' => Carbon::parse('2027-03-10'),
            ],
            [
                'name' => 'mywebsite.io',
                'tld' => 'io',
                'registrar' => 'Namecheap',
                'status' => 'active',
                'registered_at' => Carbon::parse('2022-07-01'),
                'expires_at' => Carbon::parse('2025-07-01'),
            ],
        ];

        foreach ($domains as $domainData) {
            $domain = Domain::create($domainData);

            // Create 2-3 WHOIS records for each domain
            $recordCount = rand(2, 3);
            for ($i = 0; $i < $recordCount; $i++) {
                WhoisRecord::create([
                    'domain_id' => $domain->id,
                    'raw_whois_data' => $this->generateSampleWhoisData($domain->name),
                    'registrar' => $domainData['registrar'],
                    'creation_date' => $domainData['registered_at'],
                    'expiration_date' => $domainData['expires_at'],
                    'updated_date' => Carbon::now()->subDays(rand(1, 30)),
                    'nameservers' => [
                        'ns1.' . $domain->name,
                        'ns2.' . $domain->name,
                    ],
                    'status' => 'clientTransferProhibited',
                    'registrant_name' => 'Domain Owner',
                    'registrant_email' => 'admin@' . $domain->name,
                    'registrant_organization' => ucfirst(explode('.', $domain->name)[0]) . ' Inc.',
                    'created_at' => Carbon::now()->subDays(rand($i * 10, $i * 10 + 10)),
                ]);
            }
        }
    }

    private function generateSampleWhoisData(string $domain): string
    {
        return "Domain Name: {$domain}\n" .
               "Registry Domain ID: D" . rand(100000, 999999) . "-CNIC\n" .
               "Registrar WHOIS Server: whois.example.com\n" .
               "Registrar URL: http://www.example.com\n" .
               "Updated Date: " . Carbon::now()->subDays(rand(1, 30))->toDateTimeString() . "\n" .
               "Creation Date: 2020-01-15T00:00:00Z\n" .
               "Registrar Registration Expiration Date: 2026-01-15T00:00:00Z\n" .
               "Registrar: Example Registrar\n" .
               "Domain Status: clientTransferProhibited\n" .
               "Registrant Name: Domain Owner\n" .
               "Name Server: ns1.{$domain}\n" .
               "Name Server: ns2.{$domain}\n";
    }
}

