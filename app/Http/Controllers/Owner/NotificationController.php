<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\OwnerNotification;
use App\Services\OwnerNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request, OwnerNotificationService $service): JsonResponse
    {
        $service->sync();

        $filter = (string) $request->input('filter', 'all');

        $query = OwnerNotification::query()
            ->orderByDesc('occurred_at')
            ->orderByDesc('id');

        if ($filter === 'unread') {
            $query->unread();
        }

        $items = $query->take(40)->get();
        $unreadCount = OwnerNotification::query()->unread()->count();

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
        $notification = OwnerNotification::findOrFail($id);

        if (! $notification->isRead()) {
            $notification->update([
                'read_at' => now('Asia/Jakarta'),
            ]);
        }

        return response()->json([
            'ok' => true,
            'unread_count' => OwnerNotification::query()->unread()->count(),
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        OwnerNotification::query()
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
