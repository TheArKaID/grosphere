<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ContentMustBeFileOrString implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_string($value)) {
            return true; // Validate as string
        }

        // Validate as file
        return $value->isValid() &&
               in_array($value->extension(), ['pdf', 'mp4', 'mov', 'jpg', 'jpeg', 'png']) &&
               $value->getSize() <= 100 * 1024 * 1024;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a file (pdf, mp4, mov, jpg, jpeg, or png up to 100MB) or a string.';
    }
}
