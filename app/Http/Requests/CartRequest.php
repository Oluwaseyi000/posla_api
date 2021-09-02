<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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
        $validation = [
            'proposal_id' => [
                'required_without:deal_type_id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->input('deal_type_id')) {
                        return $fail($attribute.' can only be filled when deal_type_id is empty.');
                    }
                },
                'exists:proposals,id'
            ],

            'deal_type_id' => [
                'required_without:proposal_id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->input('proposal_id')) {
                        return $fail($attribute.' can only be filled when proposal_id is empty.');
                    }
                },
                'exists:deal_types,id'
            ]
        ];

        if(Route::currentRouteName() == 'payment.paystack'){
            $validation=  array_merge($validation, ['transaction_reference' => ['required']]);
        }

        return $validation;
    }
}
