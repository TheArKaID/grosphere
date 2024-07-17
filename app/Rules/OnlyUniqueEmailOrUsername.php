<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OnlyUniqueEmailOrUsername implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (User::withoutGlobalScope('agency')->where('email', $value)->orWhere('username', $value)->exists()) {
            $fail("The $attribute has already been taken.");
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        if (preg_match('/[A-Za-z0-9]+/', $value)) {
            return;
        }

        $fail("The $attribute must be a valid email or username.");
    }
}
