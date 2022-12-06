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

class CheckSubscriptionTest extends TestCase
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

    public function testCheckOnSuccess()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $round = Run::insert(1);
        self::HttpSuccessMockHandler();
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(StatusEnum::Active, $newApp->status);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
    }

    public function testCheckOnFault()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $round = Run::insert(1);
        self::HttpFaultMockHandler();
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(StatusEnum::Pending, $newApp->status);
        $this->assertEquals(RunStatusEnum::Pending, $newRound->status);
        $this->assertEquals(1, $newRound->waiting_task);
        $this->assertEquals(0, $newRound->expired_count);
    }

    public function testIsCheckAgainWhenFault()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $round = Run::insert(1);
        self::HttpFaultMockHandler();
        $numberOfJobs = Queue::size();
        CheckService::check($app,$round);
        $lastJob = DB::table('jobs')->orderByDesc('id')->first();
        $newNumberOfJobs = Queue::size();
        $this->assertEquals(++$numberOfJobs , $newNumberOfJobs);
        $this->assertEquals($lastJob->available_at - $lastJob->created_at , $platforms[0]->provider_object()->reCheckStatusOnErrorOccurred());
    }

    public function testSuccessHttpButStatusNotValid()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("nothing");
        $numberOfJobs = Queue::size();
        CheckService::check($app,$round);
        $newNumberOfJobs = Queue::size();
        $this->assertEquals(++$numberOfJobs , $newNumberOfJobs);
    }


    public function testCheckMultiAppAndPlatformOnSuccess()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app2 = AppsService::insert('test 2 check' , null ,$platforms[1] );
        $round = Run::insert(2);
        self::HttpSuccessMockHandler();
        CheckService::check($app,$round);
        CheckService::check($app2,$round);
        $newApp = AppsService::getAppByID($app->id);
        $newApp2 = AppsService::getAppByID($app2->id);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(StatusEnum::Active, $newApp->status);
        $this->assertEquals(StatusEnum::Active, $newApp2->status);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
    }

    public function testCheckMultiAppAndPlatformOnFault()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app2 = AppsService::insert('test 2 check' , null ,$platforms[1] );
        $round = Run::insert(2);
        self::HttpFaultMockHandler();
        CheckService::check($app,$round);
        CheckService::check($app2,$round);
        $newApp = AppsService::getAppByID($app->id);
        $newApp2 = AppsService::getAppByID($app2->id);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(StatusEnum::Pending, $newApp->status);
        $this->assertEquals(StatusEnum::Pending, $newApp2->status);
        $this->assertEquals(RunStatusEnum::Pending, $newRound->status);
        $this->assertEquals(2, $newRound->waiting_task);
        $this->assertEquals(0, $newRound->expired_count);
    }



    public function testIsCheckAgainWhenFaultOnMultiAppAndPlatform()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app2 = AppsService::insert('test2 check' , null ,$platforms[1] );
        $round = Run::insert(2);
        self::HttpFaultMockHandler();
        $numberOfJobs = Queue::size();
        CheckService::check($app,$round);
        $lastJob = DB::table('jobs')->orderByDesc('id')->first();
        CheckService::check($app2,$round);
        $lastJob2 = DB::table('jobs')->orderByDesc('id')->first();
        $newNumberOfJobs = Queue::size();
        $this->assertEquals($numberOfJobs + 2 , $newNumberOfJobs);
        $this->assertEquals($lastJob->available_at - $lastJob->created_at , $platforms[0]->provider_object()->reCheckStatusOnErrorOccurred());
        $this->assertEquals($lastJob2->available_at - $lastJob2->created_at , $platforms[1]->provider_object()->reCheckStatusOnErrorOccurred());
    }

    public function testCheckStatusChangeFromPendingToActive()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("active");
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Active, $newApp->status);
    }

    public function testCheckStatusChangeFromPendingToExpire()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("Expired");
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Expired, $newApp->status);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
        $this->assertEquals(1, $newRound->expired_count);
    }

    public function testCheckStatusChangeFromPendingToPending()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("pending");
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Pending, $newApp->status);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
        $this->assertEquals(0, $newRound->expired_count);
    }

    public function testCheckStatusChangeFromActiveToPending()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app->status = StatusEnum::Active;
        $app->saveOrFail();
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("pending");
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Pending, $newApp->status);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
        $this->assertEquals(0, $newRound->expired_count);
    }

    public function testCheckStatusChangeFromActiveToActive()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app->status = StatusEnum::Active;
        $app->saveOrFail();
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("active");
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Active, $newApp->status);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
        $this->assertEquals(0, $newRound->expired_count);
    }

    public function testCheckStatusChangeFromActiveToExpired()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app->status = StatusEnum::Active;
        $app->saveOrFail();
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("Expired");
        $NumberOfJobs = Queue::size();
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Expired, $newApp->status);
        $newRound = CheckService::getRunByID($round->id);
        $newNumberOfJobs = Queue::size();
        $this->assertEquals(RunStatusEnum::Pending, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
        $this->assertEquals(1, $newRound->expired_count);
        $this->assertEquals(++$NumberOfJobs, $newNumberOfJobs);
    }

    public function testCheckStatusChangeFromExpireToActive()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app->status = StatusEnum::Expired;
        $app->saveOrFail();
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("active");
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Active, $newApp->status);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
        $this->assertEquals(0, $newRound->expired_count);
    }

    public function testCheckStatusChangeFromExpireToExpire()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app->status = StatusEnum::Expired;
        $app->saveOrFail();
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("Expired");
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Expired, $newApp->status);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
        $this->assertEquals(1, $newRound->expired_count);
    }

    public function testCheckStatusChangeFromExpireToPending()
    {
        $platforms = PlatformsService::listPlatforms() ;
        $app = AppsService::insert('test check' , null ,$platforms[0] );
        $app->status = StatusEnum::Expired;
        $app->saveOrFail();
        $round = Run::insert(1);
        self::HttpSuccessMockHandler("Pending");
        CheckService::check($app,$round);
        $newApp = AppsService::getAppByID($app->id);
        $this->assertEquals(StatusEnum::Pending, $newApp->status);
        $newRound = CheckService::getRunByID($round->id);
        $this->assertEquals(RunStatusEnum::Finished, $newRound->status);
        $this->assertEquals(0, $newRound->waiting_task);
        $this->assertEquals(0, $newRound->expired_count);
    }
}
