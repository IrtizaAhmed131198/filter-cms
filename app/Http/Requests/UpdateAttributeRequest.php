<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $attributeId = $this->route('attribute')->id ?? null;

        return [
            'name'  => 'required|string|max:255|unique:attributes,name,' . $attributeId,
            'value' => 'nullable|array',
            'value.*' => 'string'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Attribute name is required.',
            'name.unique'   => 'This attribute name already exists.',
        ];
    }
}
