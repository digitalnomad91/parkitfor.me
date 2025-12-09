@extends('layouts.app')

@section('title', 'Domains')

@section('content')
<h1 style="margin-bottom: 2rem; color: #2c3e50;">All Domains</h1>

<div class="card">
    <div class="card-header">
        Domain List ({{ $domains->total() }} total)
    </div>
    
    @if($domains->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Domain Name</th>
                <th>TLD</th>
                <th>Registrar</th>
                <th>Status</th>
                <th>Registered At</th>
                <th>Expires At</th>
                <th>WHOIS Records</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($domains as $domain)
            <tr>
                <td>{{ $domain->id }}</td>
                <td><strong>{{ $domain->name }}</strong></td>
                <td>{{ $domain->tld }}</td>
                <td>{{ $domain->registrar ?? 'N/A' }}</td>
                <td>
                    <span style="
                        padding: 0.25rem 0.75rem;
                        border-radius: 12px;
                        font-size: 0.875rem;
                        background-color: {{ $domain->status === 'active' ? '#d4edda' : '#f8d7da' }};
                        color: {{ $domain->status === 'active' ? '#155724' : '#721c24' }};
                    ">
                        {{ ucfirst($domain->status) }}
                    </span>
                </td>
                <td>{{ $domain->registered_at?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $domain->expires_at?->format('Y-m-d') ?? 'N/A' }}</td>
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
        No domains found. Run the WHOIS lookup command to add domains:
        <br><br>
        <code style="background-color: #f8f9fa; padding: 0.5rem 1rem; border-radius: 4px;">
            php artisan whois:lookup example.com
        </code>
    </p>
    @endif
</div>
@endsection
