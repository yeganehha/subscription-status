<?php

namespace Database\Seeders;

use App\Models\App;
use App\Models\Run;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ( App::all('id') as $app)
            foreach (Run::all('id') as $run)
                \App\Models\Subscription::factory(1)->appAndRun($app->id, $run->id)->create();
    }
}
