@extends('layouts.app')

@section('title', "WHOIS History: {$domain->name}")

@section('content')
@php
    $nextDirection = function ($column) use ($sort, $direction) {
        return ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
    };
@endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <p style="color: #7f8c8d; margin-bottom: 0.35rem;">WHOIS History</p>
        <h1 style="color: #2c3e50; margin: 0;">{{ $domain->name }}</h1>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('domains.show', $domain->id) }}" class="btn btn-secondary">← Domain Details</a>
        <a href="{{ route('domains') }}" class="btn btn-secondary">All Domains</a>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <span>WHOIS Records for {{ $domain->name }}</span>
        <small style="color: #7f8c8d;">Click any row to open the raw JSON payload</small>
    </div>

    @if($whoisRecords->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th><a href="{{ route('domains.whois-records', ['domain' => $domain->id, 'sort' => 'id', 'direction' => $nextDirection('id')]) }}">ID</a></th>
                        <th><a href="{{ route('domains.whois-records', ['domain' => $domain->id, 'sort' => 'registrar', 'direction' => $nextDirection('registrar')]) }}">Registrar</a></th>
                        <th><a href="{{ route('domains.whois-records', ['domain' => $domain->id, 'sort' => 'creation_date', 'direction' => $nextDirection('creation_date')]) }}">Created</a></th>
                        <th><a href="{{ route('domains.whois-records', ['domain' => $domain->id, 'sort' => 'expiration_date', 'direction' => $nextDirection('expiration_date')]) }}">Expires</a></th>
                        <th><a href="{{ route('domains.whois-records', ['domain' => $domain->id, 'sort' => 'updated_date', 'direction' => $nextDirection('updated_date')]) }}">Updated</a></th>
                        <th><a href="{{ route('domains.whois-records', ['domain' => $domain->id, 'sort' => 'status', 'direction' => $nextDirection('status')]) }}">Status</a></th>
                        <th>Nameservers</th>
                        <th><a href="{{ route('domains.whois-records', ['domain' => $domain->id, 'sort' => 'created_at', 'direction' => $nextDirection('created_at')]) }}">Looked Up At</a></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($whoisRecords as $record)
                        <tr class="clickable-row"
                            data-json-modal="true"
                            data-json="{{ base64_encode(json_encode($record->toArray(), JSON_PRETTY_PRINT)) }}"
                            data-json-title="WHOIS Record #{{ $record->id }}">
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->registrar ?? 'N/A' }}</td>
                            <td>{{ $record->creation_date?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td>{{ $record->expiration_date?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td>{{ $record->updated_date?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td>{{ $record->status ?? 'N/A' }}</td>
                            <td>
                                @if($record->nameservers && is_array($record->nameservers))
                                    <small>{{ implode(', ', array_slice($record->nameservers, 0, 3)) }}</small>
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

        <div class="mt-3">
            {{ $whoisRecords->links() }}
        </div>
    @else
        <p style="color: #7f8c8d; padding: 1rem 0;">No WHOIS records captured for this domain yet.</p>
    @endif
</div>
@endsection
