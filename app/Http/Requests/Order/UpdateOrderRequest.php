<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'date_of_creation' => ['nullable|date', 'date_format:Y-m-d H:i:s'],
            'closing_date' => ['nullable|date', 'date_format:Y-m-d H:i:s'],
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
