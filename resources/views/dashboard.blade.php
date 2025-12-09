@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 style="margin-bottom: 2rem; color: #2c3e50;">Dashboard</h1>

<div class="grid">
    <div class="stat-card">
        <h3>Total Domains</h3>
        <div class="stat-value">{{ $domains->total() }}</div>
    </div>
    
    <div class="stat-card">
        <h3>Total WHOIS Records</h3>
        <div class="stat-value">{{ $whoisRecords->total() }}</div>
    </div>
    
    <div class="stat-card">
        <h3>Recent Lookups</h3>
        <div class="stat-value">{{ $whoisRecords->count() }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Recent Domains
    </div>
    
    @if($domains->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Domain Name</th>
                <th>TLD</th>
                <th>Status</th>
                <th>WHOIS Records</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($domains as $domain)
            <tr>
                <td>{{ $domain->id }}</td>
                <td>{{ $domain->name }}</td>
                <td>{{ $domain->tld }}</td>
                <td>{{ $domain->status }}</td>
                <td>{{ $domain->whois_records_count }}</td>
                <td>{{ $domain->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="mt-3">
        {{ $domains->links() }}
    </div>
    @else
    <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
        No domains found. Run the WHOIS lookup command to add domains.
    </p>
    @endif
</div>

<div class="card">
    <div class="card-header">
        Recent WHOIS Lookups
    </div>
    
    @if($whoisRecords->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Domain</th>
                <th>Registrar</th>
                <th>Creation Date</th>
                <th>Expiration Date</th>
                <th>Looked Up At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($whoisRecords as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ $record->domain->name ?? 'N/A' }}</td>
                <td>{{ $record->registrar ?? 'N/A' }}</td>
                <td>{{ $record->creation_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $record->expiration_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="mt-3">
        {{ $whoisRecords->links() }}
    </div>
    @else
    <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
        No WHOIS records found. Run the WHOIS lookup command to add records.
    </p>
    @endif
</div>
@endsection
