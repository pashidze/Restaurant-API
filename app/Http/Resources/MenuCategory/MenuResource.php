<?php

namespace App\Http\Resources\MenuCategory;

use App\Http\Resources\Dish\DishResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'dishes' => DishResource::collection($this->whenLoaded('dishes')),
        ];
    }
}
