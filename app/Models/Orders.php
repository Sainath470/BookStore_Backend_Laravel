<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $table = "Orders";
    protected $fillable = [
        'customer_id', 'orderNumber',
        'order_date'
    ];
}
