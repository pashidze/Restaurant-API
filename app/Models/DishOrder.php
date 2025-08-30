<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DishOrder extends Model
{
    use HasFactory;

    protected $table = 'dish_orders';

    protected $fillable = [
        'order_id',
        'dish_id',
    ];
}
