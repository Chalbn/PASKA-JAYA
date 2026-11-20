<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\OwnerNotification;
use App\Services\RoleNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request, RoleNotificationService $service): JsonResponse
    {
        $user = Auth::user();
        $user?->loadMissing('role');

        $role = strtolower((string) ($user?->role?->nama ?? ''));
        $userId = $user?->id ? (int) $user->id : null;

        if ($role === '') {
            return response()->json([
                'unread_count' => 0,
                'notifications' => [],
            ]);
        }

        $service->syncForRole($role, $userId);

        $filter = (string) $request->input('filter', 'all');

        $query = OwnerNotification::query()
            ->forTarget($role, $userId)
            ->orderByDesc('occurred_at')
            ->orderByDesc('id');

        if ($filter === 'unread') {
            $query->unread();
        }

        $items = $query->take(40)->get();
        $unreadCount = OwnerNotification::query()
            ->forTarget($role, $userId)
            ->unread()
            ->count();

        Carbon::setLocale('id');

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $items->map(static function (OwnerNotification $item): array {
                $occurred = $item->occurred_at?->timezone('Asia/Jakarta');
                if ($occurred) {
                    $occurred->locale('id');
                }

                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'title' => $item->title,
                    'message' => $item->message,
                    'url' => $item->url,
                    'is_read' => $item->isRead(),
                    'time_ago' => $occurred ? $occurred->diffForHumans() : '-',
                ];
            })->values(),
        ]);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $user = Auth::user();
        $user?->loadMissing('role');

        $role = strtolower((string) ($user?->role?->nama ?? ''));
        $userId = $user?->id ? (int) $user->id : null;

        $notification = OwnerNotification::query()
            ->forTarget($role, $userId)
            ->findOrFail($id);

        if (! $notification->isRead()) {
            $notification->update([
                'read_at' => now('Asia/Jakarta'),
            ]);
        }

        return response()->json([
            'ok' => true,
            'unread_count' => OwnerNotification::query()
                ->forTarget($role, $userId)
                ->unread()
                ->count(),
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $user?->loadMissing('role');

        $role = strtolower((string) ($user?->role?->nama ?? ''));
        $userId = $user?->id ? (int) $user->id : null;

        OwnerNotification::query()
            ->forTarget($role, $userId)
            ->unread()
            ->update([
                'read_at' => now('Asia/Jakarta'),
            ]);

        return response()->json([
            'ok' => true,
            'unread_count' => 0,
        ]);
    }
}
