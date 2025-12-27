@extends('layouts.app')

@section('title', "Domain: {$domain->name}")

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <p style="color: #7f8c8d; margin-bottom: 0.35rem;">Domain Details</p>
        <h1 style="color: #2c3e50; margin: 0;">{{ $domain->name }}</h1>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <form method="POST" action="{{ route('domains.dns-lookup', $domain->id) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary">🔄 Refresh DNS</button>
        </form>
        <a href="{{ route('domains') }}" class="btn btn-secondary">← Back to Domains</a>
    </div>
</div>

<div class="grid">
    <div class="stat-card">
        <h3>Status</h3>
        <div class="stat-value" style="font-size: 1.6rem;">
            {{ ucfirst($domain->status ?? 'unknown') }}
        </div>
        <p style="color: #7f8c8d; margin-top: 0.5rem;">Registrar: {{ $domain->registrar ?? ($latestWhois->registrar ?? 'N/A') }}</p>
    </div>
    <div class="stat-card">
        <h3>WHOIS Records</h3>
        <div class="stat-value">{{ $domain->whois_records_count }}</div>
        <p style="color: #7f8c8d; margin-top: 0.5rem;">Last lookup: {{ $latestWhois?->created_at?->diffForHumans() ?? 'Never' }}</p>
    </div>
    <div class="stat-card">
        <h3>DNS Records</h3>
        <div class="stat-value">{{ $domain->dns_records_count }}</div>
        <p style="color: #7f8c8d; margin-top: 0.5rem;">Unique types: {{ $domain->dnsRecords()->distinct('record_type')->count('record_type') }}</p>
    </div>
    <div class="stat-card">
        <h3>Scrapes</h3>
        <div class="stat-value">{{ $domain->scrapes_count }}</div>
        <p style="color: #7f8c8d; margin-top: 0.5rem;">Latest scrape: {{ $recentScrapes->first()?->scraped_at?->diffForHumans() ?? 'Never' }}</p>
    </div>
</div>

<div class="card" id="whois-section" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Current WHOIS Snapshot</span>
        <a href="{{ route('domains.whois-records', $domain->id) }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">View All WHOIS</a>
    </div>

    @if($latestWhois)
        <div class="grid" style="margin-bottom: 1rem;">
            <div class="stat-card" style="border-left-color: #2ecc71;">
                <h3>Registrar</h3>
                <div class="stat-value" style="font-size: 1.25rem;">{{ $latestWhois->registrar ?? 'N/A' }}</div>
                <p style="color: #7f8c8d; margin-top: 0.4rem;">Status: {{ $latestWhois->status ?? 'N/A' }}</p>
            </div>
            <div class="stat-card" style="border-left-color: #3498db;">
                <h3>Created</h3>
                <div class="stat-value" style="font-size: 1.25rem;">{{ $latestWhois->creation_date?->format('Y-m-d') ?? 'N/A' }}</div>
                <p style="color: #7f8c8d; margin-top: 0.4rem;">Updated: {{ $latestWhois->updated_date?->format('Y-m-d') ?? 'N/A' }}</p>
            </div>
            <div class="stat-card" style="border-left-color: #e67e22;">
                <h3>Expiration</h3>
                <div class="stat-value" style="font-size: 1.25rem;">{{ $latestWhois->expiration_date?->format('Y-m-d') ?? 'N/A' }}</div>
                <p style="color: #7f8c8d; margin-top: 0.4rem;">Nameservers: {{ $latestWhois->nameservers ? implode(', ', array_slice($latestWhois->nameservers, 0, 3)) : 'N/A' }}</p>
            </div>
        </div>
    @else
        <p style="color: #7f8c8d;">No WHOIS data yet for this domain. Run a lookup to populate the snapshot.</p>
    @endif
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Recent WHOIS Records (latest 5)</span>
        <small style="color: #7f8c8d;">Click a row to view the raw JSON</small>
    </div>

    @if($recentWhoisRecords->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Registrar</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Expires</th>
                        <th>Nameservers</th>
                        <th>Looked Up At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentWhoisRecords as $record)
                        <tr class="clickable-row"
                            data-json-modal="true"
                            data-json="{{ base64_encode(json_encode($record->toArray(), JSON_PRETTY_PRINT)) }}"
                            data-json-title="WHOIS Record #{{ $record->id }}">
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->registrar ?? 'N/A' }}</td>
                            <td>{{ $record->status ?? 'N/A' }}</td>
                            <td>{{ $record->creation_date?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td>{{ $record->expiration_date?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td>
                                @if($record->nameservers)
                                    <small>{{ implode(', ', array_slice($record->nameservers, 0, 2)) }}</small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color: #7f8c8d; padding: 1rem 0;">No WHOIS records yet.</p>
    @endif
</div>

<hr class="section-divider">

<div class="card" id="dns-section">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span>DNS Records</span>
        <div>
            <a href="{{ route('domains.dns-records', $domain->id) }}" class="btn btn-secondary" style="margin-right: 0.5rem;">Open Full DNS Page</a>
            <form method="POST" action="{{ route('domains.dns-lookup', $domain->id) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary">Refresh Lookup</button>
            </form>
        </div>
    </div>

    @if($dnsRecords->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Value</th>
                        <th>TTL</th>
                        <th>Priority</th>
                        <th>Recorded At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dnsRecords as $record)
                        <tr>
                            <td>{{ $record->record_type }}</td>
                            <td>{{ $record->name }}</td>
                            <td style="max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $record->value }}">
                                <code>{{ $record->value }}</code>
                            </td>
                            <td>{{ $record->ttl ?? 'N/A' }}</td>
                            <td>{{ $record->priority ?? 'N/A' }}</td>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color: #7f8c8d; padding: 1rem 0;">No DNS records yet for this domain.</p>
    @endif
</div>

<hr class="section-divider">

<div class="card" id="scrapes-section">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Recent Scrapes (latest 5)</span>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <form method="POST" action="{{ route('domains.scrape', $domain->id) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary">🌐 Scrape Now</button>
            </form>
            <a href="{{ route('domains.scrapes', $domain->id) }}" class="btn btn-secondary">View All Scrapes</a>
        </div>
    </div>

    @if($recentScrapes->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>URL</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>HTTP</th>
                        <th>Assets</th>
                        <th>Links</th>
                        <th>Screenshot</th>
                        <th>Scraped At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentScrapes as $scrape)
                        <tr>
                            <td>{{ $scrape->id }}</td>
                            <td style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <a href="{{ $scrape->url }}" target="_blank" style="color: #3498db;">{{ $scrape->url }}</a>
                            </td>
                            <td style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $scrape->title ?? 'N/A' }}
                            </td>
                            <td>{{ ucfirst($scrape->status ?? 'unknown') }}</td>
                            <td>{{ $scrape->http_status_code ?? '—' }}</td>
                            <td>{{ $scrape->assets_count ?? $scrape->assets()->count() }}</td>
                            <td>{{ $scrape->links_count ?? $scrape->links()->count() }}</td>
                            <td>{{ $scrape->screenshot_path ? '✓' : '✗' }}</td>
                            <td>{{ $scrape->scraped_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color: #7f8c8d; padding: 1rem 0;">No scrapes captured for this domain yet.</p>
    @endif
</div>
@endsection
