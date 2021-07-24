<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookStoreUsers extends Model
{
    use HasFactory;

    protected $table = "_book_store_users_";
    protected $fillable = [
        'fullName',
        'email',
        'password',
        'mobile',
    ];
}
