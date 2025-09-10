<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class FilterUserRequest extends FormRequest
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
            'name' => 'nullable|string|max:255|',
            'email' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'sort_by' => 'nullable|string|in:id,name,role_id',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ];
    }
}
