<?php

namespace App\Rules;

use App\Models\Proposal;
use Illuminate\Contracts\Validation\Rule;

class AlreadyBided implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        return !Proposal::where(['user_id' => auth()->user()->id, 'project_id' => $value])->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "You've already bided for this project";
    }
}
