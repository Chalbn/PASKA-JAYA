<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OwnerNotification extends Model
{
    protected $table = 'owner_notifications';

    protected $fillable = [
        'event_key',
        'type',
        'title',
        'message',
        'url',
        'read_at',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'occurred_at' => 'datetime',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
