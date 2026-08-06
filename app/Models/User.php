<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = ['name', 'username', 'password', 'role_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function bidang(): ?Bidang
    {
        return $this->role?->bidang;
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    /**
     * Cek apakah user ini boleh mengisi realisasi untuk bidang tertentu
     * (dulu dicek dengan string compare $_SESSION['role'] === 'Umum dan Kepegawaian' dst,
     * sekarang cukup bandingkan bidang_id).
     */
    public function canInputBidang(Bidang $bidang): bool
    {
        return $this->role?->bidang_id === $bidang->id;
    }
}
