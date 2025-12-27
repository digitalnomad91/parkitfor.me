@extends('layouts.app')

@section('title', 'DNS Records')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="color: #2c3e50; margin-bottom: 0.5rem;">DNS Records</h1>
        <p style="color: #7f8c8d; margin: 0;">Domain: <strong>{{ $domain->name }}</strong></p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <form method="POST" action="{{ route('domains.dns-lookup', $domain->id) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">
                🔄 Refresh DNS Records
            </button>
        </form>
        <a href="{{ route('domains.show', $domain->id) }}" class="btn btn-primary">
            📄 Domain Details
        </a>
        <a href="{{ route('domains') }}" class="btn btn-secondary">
            ← Back to Domains
        </a>
    </div>
</div>

<div class="grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Total DNS Records</h3>
        <div class="stat-value">{{ $dnsRecords->total() }}</div>
    </div>

    <div class="stat-card">
        <h3>Record Types</h3>
        <div class="stat-value">{{ $domain->dnsRecords()->distinct('record_type')->count('record_type') }}</div>
    </div>

    <div class="stat-card">
        <h3>Last Updated</h3>
        <div class="stat-value" style="font-size: 1.25rem;">
            {{ $domain->dnsRecords()->latest()->first()?->created_at->diffForHumans() ?? 'Never' }}
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        DNS Records for {{ $domain->name }} ({{ $dnsRecords->total() }} total)
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
                    <td>
                        @php
                            $recordTypeColors = [
                                'A' => '#3498db',
                                'AAAA' => '#9b59b6',
                                'MX' => '#e74c3c',
                                'NS' => '#2ecc71',
                                'CNAME' => '#f39c12',
                                'TXT' => '#1abc9c',
                                'SOA' => '#34495e',
                            ];
                            $color = $recordTypeColors[$record->record_type] ?? '#95a5a6';
                        @endphp
                        <span style="
                            padding: 0.25rem 0.75rem;
                            border-radius: 12px;
                            font-size: 0.875rem;
                            font-weight: 600;
                            background-color: {{ $color }};
                            color: white;
                        ">
                            {{ $record->record_type }}
                        </span>
                    </td>
                    <td>{{ $record->name }}</td>
                    <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $record->value }}">
                        <code style="font-size: 0.875rem;">{{ $record->value }}</code>
                    </td>
                    <td>{{ $record->ttl ?? 'N/A' }}</td>
                    <td>{{ $record->priority ?? 'N/A' }}</td>
                    <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $dnsRecords->links() }}
    </div>
    @else
    <div style="text-align: center; padding: 3rem;">
        <p style="color: #7f8c8d; font-size: 1.1rem; margin-bottom: 1rem;">
            No DNS records found for this domain yet.
        </p>
        <form method="POST" action="{{ route('domains.dns-lookup', $domain->id) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">
                🔍 Perform DNS Lookup
            </button>
        </form>
        <p style="color: #7f8c8d; margin-top: 1rem; font-size: 0.9rem;">
            Or use CLI:
            <code style="background-color: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 4px;">
                php artisan dns:lookup {{ $domain->name }}
            </code>
        </p>
    </div>
    @endif
</div>

@if($dnsRecords->count() > 0)
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        📊 Record Type Summary
    </div>
    <table>
        <thead>
            <tr>
                <th>Record Type</th>
                <th>Count</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @php
                $recordTypes = $domain->dnsRecords()
                    ->select('record_type', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                    ->groupBy('record_type')
                    ->get();

                $descriptions = [
                    'A' => 'IPv4 address',
                    'AAAA' => 'IPv6 address',
                    'MX' => 'Mail exchange server',
                    'NS' => 'Name server',
                    'CNAME' => 'Canonical name (alias)',
                    'TXT' => 'Text record',
                    'SOA' => 'Start of authority',
                    'PTR' => 'Pointer record',
                    'SRV' => 'Service record',
                ];
            @endphp

            @foreach($recordTypes as $type)
            <tr>
                <td><strong>{{ $type->record_type }}</strong></td>
                <td>{{ $type->count }}</td>
                <td style="color: #7f8c8d;">
                    {{ $descriptions[$type->record_type] ?? 'Other' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
