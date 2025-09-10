<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\DishOrder\DishOrderResource;
use App\Http\Resources\User\WaiterResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'number' => $this->number,
            'list_of_dishes' => DishOrderResource::collection($this->whenLoaded('dishes')),
            'number_of_items' => $this->number_of_items,
            'cost' => (float)$this->cost,
            'date_of_creation' => $this->date_of_creation,
            'closing_date' => $this->closing_date,
            'waiter' => new WaiterResource($this->whenLoaded('user')),
        ];
    }
}
