<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\App;
use App\Models\Run;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
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
        switch (rand(1,3)) {
            case 1:
                $last_status = StatusEnum::Active;
                break;
            case 2:
                $last_status = StatusEnum::Pending;
                break;
            default:
                $last_status = StatusEnum::Expired;
                break;
        }
        return compact('status' , 'last_status');
    }

    public function appAndRun(int $app_id ,int $run_id)
    {
        return $this->state(function ($attributes) use ($app_id , $run_id) {
            return compact('app_id', 'run_id');
        });
    }
}
