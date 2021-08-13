<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Rules\AlreadyBided;
use App\Rules\IsOwner;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class ProposalRequest extends FormRequest
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
        // $this->route('proposalBid')
        if (Route::currentRouteName() == 'proposal.bid'){
            return [
                'project_id' => ['required', Rule::exists('projects', 'id')->where('status', Project::OPEN),  new IsOwner('projects'), new AlreadyBided], 
                'amount' => ['required', 'numeric', 'min:1'], 
                'deposit' => ['sometimes', 'nullable'], 
                'comment' => ['sometimes', 'nullable'], 
            ];

        }elseif (Route::currentRouteName() == 'proposal.withdraw'){
            return [
                'proposal_id' => ['required', Rule::exists('proposals', 'id')->where('user_id', auth()->user()->id)]
            ];
        }

        return [];
    }
}
