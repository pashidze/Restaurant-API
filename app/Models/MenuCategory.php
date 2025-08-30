<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MenuCategory extends Model
{
    use HasFactory;

    protected $table = 'menu_categories';

    protected $fillable = [
        'name',
        'image'
    ];

    public function dishes()
    {
        return $this->hasMany(Dish::class, 'category_id', 'id')->chaperone();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            Storage::delete($category->image);
        });
    }
}
