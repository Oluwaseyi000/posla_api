<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'phone' => 'sometimes|nullable',
            'dob' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|in:male,female',
            'profile_image' => 'sometimes|nullable|',
            'languages' => 'sometimes|nullable',
            'short_description' => 'sometimes|nullable',
            'full_description' => 'sometimes|nullable',
            'skillsets' => 'sometimes|nullable|array',
        ];
    }
}
