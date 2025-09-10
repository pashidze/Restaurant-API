<?php

namespace App\Http\Resources\Dish;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'composition' => $this->composition,
            'calories' => $this->calories,
            'price' => (float)$this->price,
            'category_id' => $this->category_id,
        ];
    }
}
