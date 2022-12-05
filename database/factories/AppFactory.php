<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\App>
 */
class AppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        switch (rand(1,3)) {
            case 1:
                $status = StatusEnum::Active;
                break;
            case 2:
                $status = StatusEnum::Pending;
                break;
            default:
                $status = StatusEnum::Expired;
                break;
        }
        $platforms = Platform::all('id')->pluck('id');
        return [
            'uid' => fake()->unique()->domainName(),
            'name' => fake()->name(),
            'platform_id' => $platforms->random(1)[0],
            'status' => $status
        ];
    }
}
