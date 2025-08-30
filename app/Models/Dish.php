<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Dish extends Model
{
    use HasFactory;

    protected $table = 'dishes';

    protected $fillable = [
        'name',
        'image',
        'composition',
        'calories',
        'price',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id', 'id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'dish_orders', 'dish_id', 'order_id')->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();

        //Удаление картинки при удалении объекта
        static::deleting(function ($dish) {
           Storage::delete($dish->image);
        });
    }
}
