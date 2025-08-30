<?php

namespace App\Http\Requests\Dish;

use Illuminate\Foundation\Http\FormRequest;

class StoreDishRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:dishes,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'composition' => 'nullable|string|max:255',
            'calories' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:menu_categories,id',
        ];
    }
}
