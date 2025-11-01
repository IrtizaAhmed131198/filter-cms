<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // optional: add a confirm field
            'role' => 'required',
            // Profile fields
            'dob' => 'nullable|date',
            'bio' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'pic' => 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'postal' => 'nullable|string|max:20',
        ];
    }
}
