<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OwnerNotification extends Model
{
    protected $table = 'owner_notifications';

    protected $fillable = [
        'target_role',
        'target_user_id',
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
            'target_user_id' => 'integer',
            'read_at' => 'datetime',
            'occurred_at' => 'datetime',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeForTarget(Builder $query, string $role, ?int $userId = null): Builder
    {
        $query->where('target_role', $role);

        if ($role === 'karyawan' && $userId !== null) {
            $query->where(function (Builder $sub) use ($userId): void {
                $sub->whereNull('target_user_id')
                    ->orWhere('target_user_id', $userId);
            });
        }

        return $query;
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
