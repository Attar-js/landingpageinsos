<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Models\UserNotification;

class NotificationService
{
    public static function send(
        int $userId,
        string $message,
        ?string $link = null,
        string $icon = 'bell',
        string $title = 'Notifikasi Terbaru'
    ): UserNotification {
        return UserNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'link' => $link,
        ]);
    }

    public static function notifyGroupLeader(
        Group $group,
        string $message,
        ?string $link = null,
        string $icon = 'bell',
        string $title = 'Notifikasi Terbaru'
    ): ?UserNotification {
        if (!$group->leader_id) {
            return null;
        }

        return self::send((int) $group->leader_id, $message, $link, $icon, $title);
    }

    public static function notifyGroupMembers(
        Group $group,
        string $message,
        ?string $link = null,
        string $icon = 'bell',
        string $title = 'Notifikasi Terbaru'
    ): void {
        $group->loadMissing('activeMembers');

        $userIds = $group->activeMembers
            ->pluck('mahasiswa_id')
            ->filter()
            ->unique();

        foreach ($userIds as $userId) {
            self::send((int) $userId, $message, $link, $icon, $title);
        }
    }

    public static function actorName(?int $userId): string
    {
        if (!$userId) {
            return 'Sistem';
        }

        return User::find($userId)?->name ?? 'Pengguna';
    }
}
