<?php

namespace App\Rules;

use App\Platforms\PlatformInterface;
use App\Services\PlatformsService;
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
        if ( ! PlatformsService::isValidProvider($value)) {
            $fail('The '.$value.' must be provider of any platform.');
        }
    }
}
