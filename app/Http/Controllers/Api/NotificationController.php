<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // GET /api/v1/notifications
    public function index()
    {
        $user = Auth::guard('api')->user();

        $notifications = Notification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json($notifications);
    }

    // PATCH /api/v1/notifications/{notification}/read
    public function markAsRead(Notification $notification)
{
    $user = Auth::guard('api')->user();

    abort_unless($notification->notifiable_id === $user->id, 403);

    $notification->update(['read_at' => now()]);

    return response()->json($notification);
}
}