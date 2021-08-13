<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class FavoriteRequest extends FormRequest
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
        $validation = [];
        if(Route::currentRouteName() == 'favourite.add.deal'){
            $validation = [
                'deal_id' => 'required|exists:deals,id'
            ];
        }elseif(Route::currentRouteName() == 'favourite.add.project'){
            $validation = [
                'project_id' => 'required|exists:projects,id'
            ];
        }

        return $validation;

    }
}
