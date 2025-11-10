<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_id' => 'required|exists:category,id',
            'name' => 'required|string|max:255|unique:sub_category,name,' . $this->route('subcategory')->id,
            'description' => 'nullable|string',
        ];
    }
}
