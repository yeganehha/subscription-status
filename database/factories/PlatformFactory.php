<?php

namespace Database\Factories;

use App\Platforms\Providers\AndroidPlatform;
use App\Platforms\Providers\IOSPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Platform>
 */
class PlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $rand = rand(1,2);
        if ( $rand == 1 )
            return [
                'name' => 'IOS',
                'provider' => IOSPlatform::class
            ];
        return [
            'name' => 'Android',
            'provider' => AndroidPlatform::class
        ];
    }
}
