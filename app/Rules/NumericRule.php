<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NumericRule implements Rule
{
    public function passes($attribute, $value)
    {
        foreach ($value as $grocery) {
            $g = json_decode($grocery, true);
            // Check if quantity is greater than or equal to 1, 0, or null
            if ($g['quantity'] !== null && !is_numeric($g['quantity'])) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return 'Količina mora biti u formatu broja!';
    }

}
