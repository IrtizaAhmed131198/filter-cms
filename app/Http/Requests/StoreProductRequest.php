<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // You can add permission check here if needed
    }

    public function rules()
    {
        return [
            // BASIC PRODUCT FIELDS
            'category_id'       => 'required|exists:category,id',
            'sub_category_id'   => 'nullable|exists:sub_category,id',

            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',

            'base_price'        => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric|min:0|lte:base_price',

            'sku'               => 'nullable|string|max:100',
            'stock'             => 'required|integer|min:0',

            'is_charge_tax'     => 'nullable|boolean',
            'is_featured'       => 'nullable|boolean',
            'status'            => 'nullable|boolean',

            // PRIMARY IMAGE
            'image'             => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',

            // GALLERY IMAGES
            'images.*'          => 'nullable|mimes:jpg,jpeg,png,webp|max:5120',

            // SIMPLE PRODUCT ATTRIBUTES
            'product_attributes'                     => 'nullable|array',
            'product_attributes.*.attribute_id'      => 'required_with:product_attributes|exists:attributes,id',
            'product_attributes.*.value'             => 'required_with:product_attributes|string',
            'product_attributes.*.price'             => 'nullable|numeric|min:0',
            'product_attributes.*.qty'               => 'nullable|integer|min:0',

            // PRODUCT VARIANTS
            'variants'                                => 'nullable|array',
            'variants.*.attributes'                   => 'required_with:variants|array',
            'variants.*.attributes.*.attribute_id'    => 'required|exists:attributes,id',
            'variants.*.attributes.*.value'           => 'required|string',

            'variants.*.sku'                          => 'nullable|string|max:100',
            'variants.*.price'                        => 'required_with:variants|numeric|min:0',
            'variants.*.stock'                        => 'required_with:variants|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'Please select category.',
            'image.image'          => 'Primary image must be an image file.',
            'images.*.image'       => 'Gallery images must be valid image files.',
            'product_attributes.*.attribute_id.required_with' => 'Attribute ID is required.',
            'variants.*.attributes.required_with' => 'Each variant must have attributes.',
        ];
    }
}
