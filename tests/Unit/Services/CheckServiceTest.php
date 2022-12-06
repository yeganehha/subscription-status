<?php

namespace Services;

use App\Enums\RunStatusEnum;
use App\Enums\StatusEnum;
use App\Models\Run;
use App\Services\AppsService;
use App\Services\CheckService;
use App\Services\PlatformsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckServiceTest extends TestCase
{
    private function setFriday(){
        $now = Carbon::now();
        $month = $now->format('F');
        $year = $now->format('Y');
        $fourthFridayMonthly = new Carbon('first friday of ' . $month . ' ' . $year);
        Carbon::setTestNow($fourthFridayMonthly);
    }
    private function setSunday(){
        $now = Carbon::now();
        $month = $now->format('F');
        $year = $now->format('Y');
        $fourthFridayMonthly = new Carbon('first sunday of ' . $month . ' ' . $year);
        Carbon::setTestNow($fourthFridayMonthly);
    }
    public function testCanCheckInWeekEnd()
    {
        self::setFriday();
        $this->assertTrue(CheckService::canCheckToDay());
    }

    public function testCanCheckInFirstDayOfWeek()
    {
        self::setSunday();
        $this->assertFalse(CheckService::canCheckToDay());
    }

    public function testStartInWeekEnd()
    {
        self::setFriday();
        self::HttpSuccessMockHandler();
        $this->assertTrue(CheckService::start());
    }

    public function testStartInFirstDayOfWeek()
    {
        self::setSunday();
        self::HttpSuccessMockHandler();
        $this->assertFalse(CheckService::start());
    }

    public function testGetRunById()
    {
        $round = Run::insert(100);
        $find = CheckService::getRunByID($round->id);
        $this->assertEquals($round->id , $find->id);
    }

    public function testGetRunByWrongId()
    {
        $this->expectException(ModelNotFoundException::class);
        CheckService::getRunByID(0);
    }
}
