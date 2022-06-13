<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $validationArray = [
            'position' => ['sometimes', 'integer', 'min:1'],
            'description' => ['sometimes'],
            'status' => ['sometimes','in:0,1'],
            'parent_id' => ['sometimes', Rule::exists('categories','id')->where('status', true)],
        ];

        if(Route::currentRouteName() == 'admin.category.create'){
            $validationArray = array_merge($validationArray, [
               'name' => ['required', 'string', 'unique:categories,name,except,id']
            ]);
        }
        return $validationArray;
    }
}
