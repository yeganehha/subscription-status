<?php

namespace App\Rules;

use App\Platforms\PlatformInterface;
use Illuminate\Contracts\Validation\InvokableRule;

class PlatformProvider implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if (is_string($value) and class_exists($value) and is_subclass_of($value, PlatformInterface::class)) {
            $fail('The :attribute must be provider of any platform.');
        }
    }
}
