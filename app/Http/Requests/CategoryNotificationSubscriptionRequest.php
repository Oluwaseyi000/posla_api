<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class CategoryNotificationSubscriptionRequest extends FormRequest
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
        if(Route::currentRouteName() == 'category.subscribe'){
            return [
                'category_id' => ['array', 'required', 'min:1'],
                'category_id.*' => [ 'required', 'distinct', 'exists:categories,id', Rule::unique('category_notification_subscriptions','category_id')->where('user_id', auth()->user()->id)],
            ];
        }else if(Route::currentRouteName() == 'category.unsubscribe'){
            return [
                'category_id' => ['array', 'required', 'min:1'],
                'category_id.*' => [ 'required', 'distinct', 'exists:categories,id'],
            ];
        }
    }
}
