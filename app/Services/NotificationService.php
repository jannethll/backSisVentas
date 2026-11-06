<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Notifica a todos los admins que tengan activado ese tipo de alerta.
     */
    public function notifyAdmins(string $type, array $data): void
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            if (! ($admin->notification_preferences[$type] ?? true)) {
                continue; // este admin desactivó este tipo de alerta
            }

            Notification::create([
                'type' => $type,
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'data' => $data,
            ]);
        }
    }
}