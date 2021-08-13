<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;

class IsOwner implements Rule
{
    protected $table;
    protected $other_column;

    public function __construct($table, $other_column = 'user_id')
    {
        $this->table = $table;
        $this->other_column = $other_column;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !DB::table($this->table)->where(['id' => $value, $this->other_column => auth()->user()->id])->exists();

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Cant perform this action on " . $this->table . " you own";
    }
}
