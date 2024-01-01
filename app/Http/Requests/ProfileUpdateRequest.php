<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'birthday'=>'nullable|date|before:today',
            'gender'=>'nullable|in:male,female',
            'bio' => 'nullable|string|max:500',
            'profile_picture'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_details'=>'nullable|string|max:11',
           /* 'password' => 'sometimes|string|min:8|confirmed',
            'email' => 'sometimes|email|unique:users,email,' . Auth::id(),*/
        ];
    }
}
