<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
        ]);
        \App\Models\User::factory(9)->create();

        $this->call(PlatformSeeder::class);
        $this->call(AppSeeder::class);
        $this->call(RunSeeder::class);
        $this->call(SubscriptionSeeder::class);
    }
}
