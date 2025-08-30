<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayReport extends Model
{
    use HasFactory;

    protected $table = 'day_reports';

    protected $fillable = [
        'date',
        'orders_count',
        'best_waiter',
        'best_dish',
    ];
}
