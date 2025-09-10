<?php

namespace App\Http\Resources\DishOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishOrderResource extends JsonResource
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
            'price' => (float)$this->price,
            'quantity' => $this->pivot->quantity,
            'cost' => (float)$this->price * $this->pivot->quantity,
        ];
    }
}
