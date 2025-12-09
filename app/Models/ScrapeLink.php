<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapeLink extends Model
{
    protected $fillable = [
        'scrape_id',
        'url',
        'anchor_text',
        'rel',
        'target',
        'link_type',
        'is_nofollow',
        'position',
    ];

    protected $casts = [
        'is_nofollow' => 'boolean',
        'position' => 'integer',
    ];

    public function scrape(): BelongsTo
    {
        return $this->belongsTo(Scrape::class);
    }
}
