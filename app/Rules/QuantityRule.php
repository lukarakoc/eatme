<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class QuantityRule implements Rule
{
    public function passes($attribute, $value)
    {

        foreach ($value as $grocery) {
            $g = json_decode($grocery, true);
            // Check if quantity is greater than or equal to 1, 0, or null
            if ($g['quantity'] !== null && $g['quantity'] >= 0) {
                return true;
            }
        }
        return false;
    }

    public function message()
    {
        return 'Morate unijeti minimum jedan proizvod u kupovinu!';
    }
}
