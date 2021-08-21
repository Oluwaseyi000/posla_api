<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostMessageRequest extends FormRequest
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

    public function messages()
    {
        return [
            'receiver_id.not_in' => 'sender cannot be same as receiver',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'receiver_id' => ['required', 'exists:users,id', 'not_in:'.auth()->user()->id],
            'message' => ['required']
        ];
    }
}
