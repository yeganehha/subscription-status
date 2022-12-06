<?php

namespace Database\Seeders;

use App\Models\Platform;
use App\Platforms\Providers\AndroidPlatform;
use App\Platforms\Providers\IOSPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Platform::factory()->create([
            'name' => 'IOS',
            'provider' => IOSPlatform::class
        ]);
        Platform::factory()->create([
            'name' => 'Android',
            'provider' => AndroidPlatform::class
        ]);
    }
}
