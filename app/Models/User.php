<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi ke Rental
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        // Pakai ?-> (nullsafe operator) supaya aman kalau role null
        return $this->role?->name === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser()
    {
        return $this->role?->name === 'user';
    }
}