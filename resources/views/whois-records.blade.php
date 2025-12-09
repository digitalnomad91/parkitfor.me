@extends('layouts.app')

@section('title', 'WHOIS Records')

@section('content')
<h1 style="margin-bottom: 2rem; color: #2c3e50;">WHOIS Records</h1>

<div class="card">
    <div class="card-header">
        WHOIS Lookup History ({{ $whoisRecords->total() }} total)
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
                <th>Updated Date</th>
                <th>Status</th>
                <th>Nameservers</th>
                <th>Looked Up At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($whoisRecords as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td><strong>{{ $record->domain->name ?? 'N/A' }}</strong></td>
                <td>{{ $record->registrar ?? 'N/A' }}</td>
                <td>{{ $record->creation_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $record->expiration_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $record->updated_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $record->status ?? 'N/A' }}</td>
                <td>
                    @if($record->nameservers && is_array($record->nameservers))
                        <small>{{ implode(', ', array_slice($record->nameservers, 0, 2)) }}
                        @if(count($record->nameservers) > 2)
                            <br>+ {{ count($record->nameservers) - 2 }} more
                        @endif
                        </small>
                    @else
                        N/A
                    @endif
                </td>
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
        No WHOIS records found. Run the WHOIS lookup command to add records:
        <br><br>
        <code style="background-color: #f8f9fa; padding: 0.5rem 1rem; border-radius: 4px;">
            php artisan whois:lookup example.com
        </code>
        <br><br>
        Or lookup all domains:
        <br><br>
        <code style="background-color: #f8f9fa; padding: 0.5rem 1rem; border-radius: 4px;">
            php artisan whois:lookup --all
        </code>
    </p>
    @endif
</div>
@endsection
