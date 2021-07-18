<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class DealRequest extends FormRequest
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
        if(Route::currentRouteName() == 'create-deal-stage-two-info' || Route::currentRouteName() == 'edit-deal-stage-two-info'){
            $validation = [
                'title' => ['required'],
                'category_id' => ['required', 'exists:categories,id'],
                'subcategory_id' => ['required','exists:categories,id'],
                'description' => ['required'],
                'status' => ['required', 'in:1,0'],
                'tags' => ['required'],
                'pictures' => ['required', 'array'],
            ];

        }elseif(Route::currentRouteName() == 'create-deal-stage-three-price'){
            $validation = [
                'types' => 'required|array',
                'types.*.type' => 'distinct',

                'types.basic' => 'required|array',
                'types.basic.name' => ['required'],
                'types.basic.type' => ['required', 'in:basic' ],
                'types.basic.description' => ['required'],
                'types.basic.delivery_timeframe' => ['required', 'integer', 'min:1'],
                'types.basic.revision_num' => ['required', 'integer', 'min:1'],
                'types.basic.price' => ['required', 'numeric', 'min:1'],

                'types.standard' => 'sometimes|array',
                'types.standard.type' => [
                                            'required_with:types.standard.name', 
                                            'required_with:types.standard.description',
                                            'required_with:types.standard.delivery_timeframe',
                                            'required_with:types.standard.revision_num',
                                            'required_with:types.standard.price',
                                            'in:standard', 
                                         ],
                'types.standard.name' => ['required_with:types.standard.type'],
                'types.standard.description' => ['required_with:types.standard.type'],
                'types.standard.delivery_timeframe' => ['required_with:types.standard.type', 'integer', 'min:1'],
                'types.standard.revision_num' => ['required_with:types.standard.type', 'integer', 'min:1'],
                'types.standard.price' => ['required_with:types.standard.type', 'numeric', 'min:1'],

                'types.premium' => 'sometimes|array',
                'types.premium.type' => [
                                            'required_with:types.premium.name', 
                                            'required_with:types.premium.description',
                                            'required_with:types.premium.delivery_timeframe',
                                            'required_with:types.premium.revision_num',
                                            'required_with:types.premium.price',
                                            'in:premium' 
                                    ],
                'types.premium.name' => ['required_with:types.premium.type'],
                'types.premium.description' => ['required_with:types.premium.type'],
                'types.premium.delivery_timeframe' => ['required_with:types.premium.type', 'integer', 'min:1'],
                'types.premium.revision_num' => ['required_with:types.premium.type', 'integer', 'min:1'],
                'types.premium.price' => ['required_with:types.premium.type', 'numeric', 'min:1'], 
            ];
        }
        elseif(Route::currentRouteName() == 'create-deal-stage-four-requirement'){
            $validation = [
                'questions.input_type' => ['array', 'in:text, dropdown'],
                'questions.options' => ['array'],
                'questions.question' => ['array'] 
            ];
        }
        elseif(Route::currentRouteName() == 'create-project-stage-three-publish'){
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
