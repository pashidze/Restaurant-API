<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'list_of_dishes' => 'required|array',
            'number_of_items' => 'nullable|integer',
            'cost' => 'nullable|numeric',
            'date_of_creation' => ['nullable|date', 'date_format:Y-m-d H:i:s'],
            'closing_date' => ['nullable|date', 'date_format:Y-m-d H:i:s'],
            'user_id' => 'required|integer|exists:users,id',
        ];
    }
}
