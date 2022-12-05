<?php

namespace App\Services;

use App\Enums\RunStatusEnum;
use App\Enums\StatusEnum;
use App\Jobs\CheckApplicationJob;
use App\Jobs\SendEmailJob;
use App\Models\App;
use App\Models\Run;
use App\Models\Subscription;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CheckService
{
    /**
     * Find Run with ID
     * @param int|string $id
     * @return Run
     */
    public static function getRunByID(int|string $id) : Run
    {
        return Run::findById( (int) $id);
    }

    /**
     * search application
     * @param int|string|null $id
     * @param string|StatusEnum|null $lastStatus
     * @param string|StatusEnum|null $status
     * @param int|App|string|null $app
     * @param int|Run|string|null $run
     * @param int|bool|null $page
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator
     */
    public static function searchSubscription(int|null|string $id = null,string|StatusEnum|null $lastStatus = null,string|StatusEnum|null $status = null, int|App|string|null $app = null, int|Run|string|null $run = null, int|bool|null $page,int|null $perPage = 10)
    {
        if ( is_string($id) )
            $id = (int)$id;

        $app_id = null ;
        if ( is_string($app) or is_int($app) )
            $app_id = AppsService::getAppByID($app)?->id;
        elseif ($app instanceof App)
            $app_id = $app->id;

        $run_id = null ;
        if ( is_string($run) or is_int($run) )
            $run_id = self::getRunByID($run)->id;
        elseif ($run instanceof Run)
            $run_id = $run->id;


        if( is_string($status)){
            $status = StatusEnum::getFromString($status);
        }

        if( is_string($lastStatus)){
            $lastStatus = StatusEnum::getFromString($lastStatus);
        }

        if ( $page !== false)
            $page = max($page , 1);
        $perPage = min($perPage , 50);
        $perPage = max($perPage , 10);
        return Subscription::search($id ,$lastStatus , $status , $app_id , $run_id ,$page,$perPage);
    }


    /**
     * search application
     * @param int|string|null $id
     * @param mixed|null $date
     * @param bool $last
     * @param string|RunStatusEnum|null $status
     * @param int|bool|null $page
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator|Run
     */
    public static function searchRuns(int|null|string $id = null,mixed $date = null, bool $last = false,string|RunStatusEnum|null $status = null, int|bool|null $page = 1,int|null $perPage = 10)
    {
        if ( is_string($id) )
            $id = (int)$id;

        if ( is_int($date) )
            $date = Carbon::createFromTimestamp($date);
        elseif ( is_string($date) )
            $date = Carbon::make($date);
        elseif ( is_array($date) and count($date) >= 3 )
            $date = Carbon::create($date[0] , $date[1]  , $date[2]);
        elseif ( ! $date instanceof Carbon and $date !== null)
            throw new InvalidArgumentException("Invalid date [{$date}].");


        if( is_string($status)){
            $status = RunStatusEnum::getFromString($status);
        }

        if ( $page !== false)
            $page = max($page , 1);
        $perPage = min($perPage , 50);
        $perPage = max($perPage , 10);
        return Run::search($id ,$date , $last ,$status ,$page,$perPage);
    }

    public static function canCheckToDay() : bool
    {
        $activeDays = config('subscription.active_days',[]);
        $now = Carbon::now();
        foreach ($activeDays as $day){
            if ( $day == $now->dayOfWeek
                or strtolower($day) == strtolower($now->dayName)
                or strtolower($day) == strtolower($now->shortDayName) )
                return true;
        }
        return false;
    }

    /**
     * start checking applications
     * @return bool
     * @throws \Throwable
     */
    public static function start(): bool
    {
        if ( ! self::canCheckToDay() )
            return false;

        $apps = AppsService::search(null,null,null,null,null,false);

        $round =  Run::insert($apps->count());

        foreach ( $apps as $app )
            self::addApplication($app , $round);

        return true;
    }

    /**
     * check special application
     * @param App $app
     * @param Run $round
     * @return void
     * @throws \Throwable
     */
    public static function check(App $app , Run $round): void
    {
        $provider = $app->platform->provider_object();
        $provider->setApp($app);
        try {
            $status = $provider->getStatus();
            self::setStatus($status , $app ,$round);
        } catch (\Exception $exception) {
            self::addApplication($app , $round , $provider->reCheckStatusOnErrorOccurred());
        }
    }

    /**
     * update status of apps
     * @param StatusEnum $status
     * @param App $app
     * @param Run $round
     * @return void
     * @throws \Throwable
     */
    private static function setStatus(StatusEnum $status , App $app , Run $round): void
    {
        DB::beginTransaction();
        if ( $status == StatusEnum::Expired )
            $round->incrementExpiredApps();

        $waitTask = $round->decrementTask();
        Subscription::insert($app->id,$round->id,$app->status,$status);
        $app->setStatus($status);
        if ( $waitTask == 0 and $round->status == RunStatusEnum::Pending)
            self::finish($round);
        DB::commit();

    }

    /**
     * add application to queue
     * @param App $app
     * @param Run $round
     * @param int $delay
     * @return void
     */
    private static function addApplication(App $app , Run $round , int $delay = 0): void
    {
        dispatch(new CheckApplicationJob($app , $round))->delay(now()->addSeconds($delay));
    }

    /**
     * @throws \Throwable
     */
    private static function finish(Run $run): void
    {
        $subscriptions = self::searchSubscription(
            null,
            config('subscription.email_when.from' , StatusEnum::Active) ,
            config('subscription.email_when.to' , StatusEnum::Expired) ,
            null,
            $run->id,
            false);

        if ($subscriptions->count() == 0 ) {
            $run->finish();
        } else
            dispatch(new SendEmailJob($subscriptions->pluck('app_id') , $run, config('subscription') ));
    }
}
