<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Run::factory(20)->create();
    }
}
