<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        // Only allow if user has permission to update category
        return auth()->user()->hasPermission('edit_category');
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique name except for the current category being updated
                Rule::unique('category', 'name')->ignore($this->route('category')->id),
            ],
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'This category name already exists.',
            'description.max' => 'Description can not exceed 1000 characters.',
        ];
    }
}
