@extends('layouts.app')

@section('title', 'Website Scrapes')

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="color: #2c3e50; margin-bottom: 0.5rem;">Website Scrapes</h1>
        <p style="color: #7f8c8d; margin: 0;">Domain: <strong>{{ $domain->name }}</strong></p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <form method="POST" action="{{ route('domains.scrape', $domain->id) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">
                🌐 Scrape Website
            </button>
        </form>
        <a href="{{ route('domains') }}" class="btn btn-secondary">
            ← Back to Domains
        </a>
    </div>
</div>

<div class="grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Total Scrapes</h3>
        <div class="stat-value">{{ $scrapes->total() }}</div>
    </div>
    
    <div class="stat-card">
        <h3>Latest Scrape</h3>
        <div class="stat-value" style="font-size: 1.25rem;">
            {{ $scrapes->first()?->scraped_at?->diffForHumans() ?? 'Never' }}
        </div>
    </div>
    
    <div class="stat-card">
        <h3>Success Rate</h3>
        <div class="stat-value">
            @php
                $total = $domain->scrapes()->count();
                $completed = $domain->scrapes()->where('status', 'completed')->count();
                $rate = $total > 0 ? round(($completed / $total) * 100) : 0;
            @endphp
            {{ $rate }}%
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Scrape History ({{ $scrapes->total() }} total)
    </div>
    
    @if($scrapes->count() > 0)
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>HTTP Code</th>
                    <th>Assets</th>
                    <th>Links</th>
                    <th>Screenshot</th>
                    <th>Scraped At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scrapes as $scrape)
                <tr>
                    <td>{{ $scrape->id }}</td>
                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <a href="{{ $scrape->url }}" target="_blank" style="color: #3498db;">
                            {{ $scrape->url }}
                        </a>
                    </td>
                    <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $scrape->title ?? 'N/A' }}
                    </td>
                    <td>
                        <span style="
                            padding: 0.25rem 0.75rem;
                            border-radius: 12px;
                            font-size: 0.875rem;
                            font-weight: 600;
                            background-color: {{ $scrape->status === 'completed' ? '#d4edda' : ($scrape->status === 'failed' ? '#f8d7da' : '#fff3cd') }};
                            color: {{ $scrape->status === 'completed' ? '#155724' : ($scrape->status === 'failed' ? '#721c24' : '#856404') }};
                        ">
                            {{ ucfirst($scrape->status) }}
                        </span>
                    </td>
                    <td>
                        <span style="
                            padding: 0.25rem 0.75rem;
                            border-radius: 4px;
                            font-size: 0.875rem;
                            background-color: {{ $scrape->http_status_code >= 200 && $scrape->http_status_code < 300 ? '#d4edda' : '#f8d7da' }};
                        ">
                            {{ $scrape->http_status_code ?? 'N/A' }}
                        </span>
                    </td>
                    <td>{{ $scrape->assets()->count() }}</td>
                    <td>{{ $scrape->links()->count() }}</td>
                    <td>{{ $scrape->screenshot_path ? '✓' : '✗' }}</td>
                    <td>{{ $scrape->scraped_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('domains.scrape-detail', [$domain->id, $scrape->id]) }}" 
                           class="btn btn-primary" 
                           style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                            👁️ View Details
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $scrapes->links() }}
    </div>
    @else
    <div style="text-align: center; padding: 3rem;">
        <p style="color: #7f8c8d; font-size: 1.1rem; margin-bottom: 1rem;">
            No scrapes found for this domain yet.
        </p>
        <form method="POST" action="{{ route('domains.scrape', $domain->id) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">
                🌐 Scrape This Website
            </button>
        </form>
        <p style="color: #7f8c8d; margin-top: 1.5rem; font-size: 0.9rem;">
            Or use CLI: <code style="background-color: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 4px;">php artisan scrape:website {{ $domain->name }}</code>
        </p>
    </div>
    @endif
</div>

@if($scrapes->count() > 0 && $scrapes->where('status', 'completed')->count() > 0)
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        📈 Scraping Statistics
    </div>
    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @php
                $completed = $scrapes->where('status', 'completed');
                $avgAssets = $completed->avg(fn($s) => $s->assets()->count());
                $avgLinks = $completed->avg(fn($s) => $s->links()->count());
                $avgResponseTime = $completed->where('response_time_ms', '>', 0)->avg('response_time_ms');
            @endphp
            <tr>
                <td><strong>Average Assets per Scrape</strong></td>
                <td>{{ number_format($avgAssets ?? 0, 1) }}</td>
            </tr>
            <tr>
                <td><strong>Average Links per Scrape</strong></td>
                <td>{{ number_format($avgLinks ?? 0, 1) }}</td>
            </tr>
            <tr>
                <td><strong>Average Response Time</strong></td>
                <td>{{ number_format($avgResponseTime ?? 0) }} ms</td>
            </tr>
            <tr>
                <td><strong>Total Unique Assets</strong></td>
                <td>{{ \App\Models\ScrapeAsset::whereHas('scrapes', fn($q) => $q->where('domain_id', $domain->id))->count() }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif
@endsection
