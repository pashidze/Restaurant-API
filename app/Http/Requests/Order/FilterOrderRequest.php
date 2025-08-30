<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class FilterOrderRequest extends FormRequest
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
            'number' => 'nullable|string',
            'cost' => 'nullable|numeric',
            'closing_date' => 'nullable|date',
            'user_id' => 'nullable|integer|exists:users,id',
            'sort_by' => 'nullable|string',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ];
    }
}
