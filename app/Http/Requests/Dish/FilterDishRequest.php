<?php

namespace App\Http\Requests\Dish;

use Illuminate\Foundation\Http\FormRequest;

class FilterDishRequest extends FormRequest
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
            'name' => 'nullable|string|max:255|unique:menu_categories,name',
            'composition' => 'nullable|string|max:255',
            'calories' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:menu_categories,id',
            'sort_by' => 'nullable|string',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ];
    }
}
