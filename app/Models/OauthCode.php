<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OauthCode extends Model
{
    protected $guarded = [];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
}
