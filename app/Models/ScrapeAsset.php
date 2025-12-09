<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScrapeAsset extends Model
{
    protected $fillable = [
        'url',
        'type',
        'file_path',
        'mime_type',
        'file_size',
        'hash',
        'download_attempts',
        'status',
        'error_message',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
        'file_size' => 'integer',
        'download_attempts' => 'integer',
    ];

    public function scrapes(): BelongsToMany
    {
        return $this->belongsToMany(Scrape::class, 'scrape_asset_pivot');
    }
}
