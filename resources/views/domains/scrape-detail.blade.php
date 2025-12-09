@extends('layouts.app')

@section('title', 'Scrape Details')

@section('content')
<div style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="color: #2c3e50; margin-bottom: 0.5rem;">Scrape Details</h1>
            <p style="color: #7f8c8d; margin: 0;">
                Domain: <strong>{{ $domain->name }}</strong> | 
                Scraped: <strong>{{ $scrape->scraped_at?->format('Y-m-d H:i') ?? 'N/A' }}</strong>
            </p>
        </div>
        <a href="{{ route('domains.scrapes', $domain->id) }}" class="btn btn-secondary">
            ← Back to Scrapes
        </a>
    </div>
</div>

<div class="grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Status</h3>
        <div class="stat-value" style="font-size: 1.5rem;">
            <span style="
                padding: 0.25rem 0.75rem;
                border-radius: 12px;
                background-color: {{ $scrape->status === 'completed' ? '#d4edda' : '#f8d7da' }};
                color: {{ $scrape->status === 'completed' ? '#155724' : '#721c24' }};
            ">
                {{ ucfirst($scrape->status) }}
            </span>
        </div>
    </div>
    
    <div class="stat-card">
        <h3>HTTP Status</h3>
        <div class="stat-value">{{ $scrape->http_status_code ?? 'N/A' }}</div>
    </div>
    
    <div class="stat-card">
        <h3>Response Time</h3>
        <div class="stat-value" style="font-size: 1.5rem;">
            {{ $scrape->response_time_ms ?? 0 }} ms
        </div>
    </div>
</div>

@if($scrape->screenshot_path)
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        📸 Screenshot
    </div>
    <div style="padding: 1rem; text-align: center; background-color: #f8f9fa;">
        <img src="{{ asset($scrape->screenshot_path) }}" 
             alt="Website Screenshot" 
             style="max-width: 100%; border: 2px solid #ddd; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    </div>
</div>
@endif

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        📄 Page Information
    </div>
    <table>
        <tbody>
            <tr>
                <td style="width: 200px;"><strong>URL</strong></td>
                <td><a href="{{ $scrape->url }}" target="_blank" style="color: #3498db;">{{ $scrape->url }}</a></td>
            </tr>
            <tr>
                <td><strong>Title</strong></td>
                <td>{{ $scrape->title ?? 'N/A' }}</td>
            </tr>
            @if($scrape->meta_description)
            <tr>
                <td><strong>Description</strong></td>
                <td>{{ $scrape->meta_description }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Content Type</strong></td>
                <td>{{ $scrape->content_type ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Content Length</strong></td>
                <td>{{ $scrape->content_length ? number_format($scrape->content_length) . ' bytes' : 'N/A' }}</td>
            </tr>
        </tbody>
    </table>
</div>

@if($scrape->meta_tags && count($scrape->meta_tags) > 0)
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        🏷️ Meta Tags
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Content</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scrape->meta_tags as $name => $content)
            <tr>
                <td><code>{{ $name }}</code></td>
                <td style="max-width: 600px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $content }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($scrape->open_graph_data && count($scrape->open_graph_data) > 0)
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        📊 Open Graph Data
    </div>
    <table>
        <thead>
            <tr>
                <th>Property</th>
                <th>Content</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scrape->open_graph_data as $property => $content)
            <tr>
                <td><code>og:{{ $property }}</code></td>
                <td style="max-width: 600px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $content }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        📦 Assets ({{ $scrape->assets()->count() }} total)
    </div>
    
    @if($scrape->assets()->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>URL</th>
                <th>File Size</th>
                <th>MIME Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scrape->assets()->take(50)->get() as $asset)
            <tr>
                <td>
                    <span style="
                        padding: 0.25rem 0.75rem;
                        border-radius: 12px;
                        font-size: 0.875rem;
                        background-color: #e3f2fd;
                        color: #1565c0;
                    ">
                        {{ strtoupper($asset->type) }}
                    </span>
                </td>
                <td style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <a href="{{ $asset->url }}" target="_blank" style="color: #3498db; font-size: 0.875rem;">
                        {{ $asset->url }}
                    </a>
                </td>
                <td>{{ $asset->file_size ? number_format($asset->file_size / 1024, 1) . ' KB' : 'N/A' }}</td>
                <td style="font-size: 0.875rem;">{{ $asset->mime_type ?? 'N/A' }}</td>
                <td>
                    <span style="font-size: 0.875rem;">
                        {{ $asset->status === 'downloaded' ? '✓' : '✗' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($scrape->assets()->count() > 50)
    <div style="padding: 1rem; text-align: center; background-color: #f8f9fa; color: #7f8c8d;">
        Showing first 50 of {{ $scrape->assets()->count() }} assets
    </div>
    @endif
    @else
    <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
        No assets found.
    </p>
    @endif
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        🔗 Links ({{ $scrape->links()->count() }} total)
    </div>
    
    @if($scrape->links()->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>URL</th>
                <th>Anchor Text</th>
                <th>NoFollow</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scrape->links()->take(100)->get() as $link)
            <tr>
                <td>
                    <span style="
                        padding: 0.25rem 0.75rem;
                        border-radius: 12px;
                        font-size: 0.875rem;
                        background-color: {{ $link->link_type === 'internal' ? '#d4edda' : '#e3f2fd' }};
                        color: {{ $link->link_type === 'internal' ? '#155724' : '#1565c0' }};
                    ">
                        {{ ucfirst($link->link_type) }}
                    </span>
                </td>
                <td style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <a href="{{ $link->url }}" target="_blank" style="color: #3498db; font-size: 0.875rem;">
                        {{ $link->url }}
                    </a>
                </td>
                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $link->anchor_text ?? '(no text)' }}
                </td>
                <td>{{ $link->is_nofollow ? '✓' : '✗' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($scrape->links()->count() > 100)
    <div style="padding: 1rem; text-align: center; background-color: #f8f9fa; color: #7f8c8d;">
        Showing first 100 of {{ $scrape->links()->count() }} links
    </div>
    @endif
    
    <div class="card" style="margin-top: 1rem; background-color: #f8f9fa;">
        <table>
            <thead>
                <tr>
                    <th>Link Type</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $linkTypes = $scrape->links()
                        ->select('link_type', \DB::raw('count(*) as count'))
                        ->groupBy('link_type')
                        ->get();
                @endphp
                @foreach($linkTypes as $type)
                <tr>
                    <td><strong>{{ ucfirst($type->link_type) }}</strong></td>
                    <td>{{ $type->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p style="text-align: center; padding: 2rem; color: #7f8c8d;">
        No links found.
    </p>
    @endif
</div>

@if($scrape->error_message)
<div class="card" style="margin-bottom: 2rem; border-left: 4px solid #e74c3c;">
    <div class="card-header" style="background-color: #f8d7da; color: #721c24;">
        ⚠️ Error Message
    </div>
    <div style="padding: 1rem;">
        <code style="color: #e74c3c;">{{ $scrape->error_message }}</code>
    </div>
</div>
@endif
@endsection
