<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Scrape extends Model
{
    protected $fillable = [
        'domain_id',
        'url',
        'title',
        'html_body',
        'screenshot_path',
        'video_path',
        'favicon_path',
        'http_status_code',
        'http_headers',
        'response_time_ms',
        'content_type',
        'content_length',
        'meta_description',
        'meta_tags',
        'open_graph_data',
        'status',
        'error_message',
        'scraped_at',
    ];

    protected $casts = [
        'meta_tags' => 'array',
        'open_graph_data' => 'array',
        'scraped_at' => 'datetime',
        'http_status_code' => 'integer',
        'response_time_ms' => 'integer',
        'content_length' => 'integer',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(ScrapeLink::class);
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(ScrapeAsset::class, 'scrape_asset_pivot');
    }
}
