<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
{
    public function rules()
    {
        $pageId = $this->route('page');
        return [
            'label' => 'required|string|max:255',
            'slug'  => "required|string|max:255|unique:sections,slug,NULL,id,page_id,{$pageId}",
            'type'  => 'required|in:text,textarea,image,video',
        ];
    }
}
