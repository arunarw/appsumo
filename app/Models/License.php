<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $keyType = 'string';

    public $incrementing = false;

    public function oauthCodes(): HasMany
    {
        return $this->hasMany(OauthCode::class);
    }

    public function replacement(): BelongsTo
    {
        return $this->belongsTo(License::class, 'replaced_by');
    }
}
