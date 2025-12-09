<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DnsRecord extends Model
{
    protected $fillable = [
        'domain_id',
        'record_type',
        'name',
        'value',
        'ttl',
        'priority',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'ttl' => 'integer',
        'priority' => 'integer',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
