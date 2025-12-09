<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhoisRecord extends Model
{
    protected $fillable = [
        'domain_id',
        'raw_whois_data',
        'registrar',
        'creation_date',
        'expiration_date',
        'updated_date',
        'nameservers',
        'status',
        'registrant_name',
        'registrant_email',
        'registrant_organization',
    ];

    protected $casts = [
        'creation_date' => 'date',
        'expiration_date' => 'date',
        'updated_date' => 'date',
        'nameservers' => 'array',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
