<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'google_id',
        'username',
        'password',
        'avatar',
        'avatar_original',
        'email_verified_at',
        'user_id',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function verificator()
    {
        return $this->hasOne(User::class, 'id', 'user_verify');
    }
    public function adminlte_image()
    {
        if ($this->avatar) {
            return $this->avatar;
        } else {
            return 'https://picsum.photos/300/300';
        }
    }
}
