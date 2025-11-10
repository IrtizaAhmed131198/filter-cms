<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        // Only allow if user has permission to create category
        return auth()->user()->hasPermission('create_category');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:category,name',
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
