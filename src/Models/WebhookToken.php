<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookToken extends Model
{
    protected $fillable = [
        'token',
        'url',
        'is_active',
        'expires_at',
        'request_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the active webhook token.
     */
    public static function getActiveToken(): ?self
    {
        return self::where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Check if token is expired or full.
     */
    public function needsRenewal(): bool
    {
        return $this->expires_at <= now() || $this->request_count >= 100;
    }
}
