<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    protected $fillable = [
        'name',
        'tld',
        'registered_at',
        'expires_at',
        'registrar',
        'status',
        'nameservers',
        'notes',
    ];

    protected $casts = [
        'registered_at' => 'date',
        'expires_at' => 'date',
    ];

    public function whoisRecords(): HasMany
    {
        return $this->hasMany(WhoisRecord::class);
    }
}
