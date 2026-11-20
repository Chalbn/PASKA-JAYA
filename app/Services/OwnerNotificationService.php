<?php

declare(strict_types=1);

namespace App\Services;

class OwnerNotificationService
{
    public function __construct(private readonly RoleNotificationService $roleNotifications) {}

    public function sync(): void
    {
        $this->roleNotifications->syncForRole('owner');
    }
}
