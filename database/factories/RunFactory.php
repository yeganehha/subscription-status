<?php

namespace Database\Factories;

use App\Enums\RunStatusEnum;
use App\Models\App;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Run>
 */
class RunFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $allApps = App::query()->count();
        return [
            'expired_count' => rand(0,$allApps),
            'waiting_task' => 0,
            'status' => RunStatusEnum::Finished,
            'created_at' => fake()->dateTimeThisYear(),
        ];
    }
}
