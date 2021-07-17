<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'project_id.exists' => "Project does not exist or it's not in creation process"
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation = [];
        if( Route::currentRouteName() == ('create-project-stage-two-info' || 'edit-project-stage-two-info')){
            $validation = [
                'title' => ['required'],
                'category_id' => ['required', 'exists:categories,id'],
                'subcategory_id' => ['required','exists:categories,id'],
                'timeframe' => ['required', 'integer', 'min:1'],
                'budget' => ['required', 'numeric', 'min:1'],
                'active_until' => ['required','date', 'after:yesterday'],
                'description' => ['required'],
                'status' => ['required', 'in:1,0'],
                'tags' => ['required'],
                'pictures' => ['required', 'array'],
            ];

        }elseif(Route::currentRouteName() == 'create-project-stage-three-publish'){
            $validation = [
                'project_id' => ['required',  Rule::exists('projects','id')->where('user_id', auth()->user()->id)->where('action', 'creating')],
                'boosted' => 'sometimes|nullable|in:1,0',
            ];
        }
        // elseif(Route::currentRouteName()  == 'edit-project-stage-two-info'){
        //     $validation = [
        //         'title' => ['required'],
        //         'category_id' => ['required', 'exists:categories,id'],
        //         'subcategory_id' => ['required','exists:categories,id'],
        //         'timeframe' => ['required', 'integer', 'min:1'],
        //         'budget' => ['required', 'numeric', 'min:1'],
        //         'active_until' => ['required','date', 'after:yesterday'],
        //         'description' => ['required'],
        //         'status' => ['required', 'in:1,0'],
        //         'tags' => ['required'],
        //         'pictures' => ['required', 'array'],
        //     ];

        // }

        return  $validation;
    }
}
