<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class BookStoreUsers extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = "_book_store_users_";
    protected $fillable = [
        'fullName',
        'email',
        'password',
        'mobile',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
