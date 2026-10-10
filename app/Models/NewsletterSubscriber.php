<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email'];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    /** People who confirmed their address and have not left. These are the only ones we may email. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    public function isActive(): bool
    {
        return $this->confirmed_at !== null && $this->unsubscribed_at === null;
    }

    // Signed URLs: the signature is the proof of ownership, so the id alone can't be used to
    // confirm or unsubscribe somebody else's address.
    public function confirmUrl(): string
    {
        return URL::signedRoute('newsletter.confirm', ['subscriber' => $this->id]);
    }

    public function unsubscribeUrl(): string
    {
        return URL::signedRoute('newsletter.unsubscribe', ['subscriber' => $this->id]);
    }
}
