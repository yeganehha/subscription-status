<?php

namespace Console\Commands;

use App\Console\Commands\MakePlatformCommand;
use App\Services\PlatformsService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MakePlatformCommandTest extends TestCase
{
    public function testCommand()
    {
        $numberOfProviders = PlatformsService::listProviders()->count();
        $this->artisan('make:platform TestPlatform')->assertSuccessful();
        $this->artisan('make:platform TestPlatform')->assertSuccessful();
        $this->assertCount($numberOfProviders + 1 , PlatformsService::listProviders()->toArray());
        File::delete(app_path('Platforms\Providers\TestPlatform.php'));
    }
}
