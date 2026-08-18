<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    protected $table = 'system_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Send a notification to all users with a given role slug.
     */
    public static function notifyRole(string $roleSlug, string $type, string $title, string $message, ?string $link = null): void
    {
        $users = User::role($roleSlug)->get();
        foreach ($users as $user) {
            self::create([
                'user_id' => $user->id,
                'type'    => $type,
                'title'   => $title,
                'message' => $message,
                'link'    => $link,
            ]);
        }
    }

    /**
     * Send a notification to a specific user.
     */
    public static function notifyUser(int $userId, string $type, string $title, string $message, ?string $link = null): void
    {
        self::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
        ]);
    }
}
