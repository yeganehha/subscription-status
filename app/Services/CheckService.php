<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\App;
use App\Models\Platform;
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
     * @param string|StatusEnum|null $status
     * @param int|App|string|null $app
     * @param int|Run|string|null $run
     * @param int|bool|null $page
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator
     */
    public static function searchSubscription(int|null|string $id = null,string|StatusEnum|null $status = null, int|App|string|null $app = null, int|Run|string|null $run = null, int|bool|null $page,int|null $perPage = 10)
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

        $page = max($page , 1);
        $perPage = min($perPage , 50);
        $perPage = max($perPage , 10);
        return Subscription::search($id ,$status , $app_id , $run_id ,$page,$perPage);
    }


    /**
     * search application
     * @param int|string|null $id
     * @param mixed|null $date
     * @param bool $last
     * @param int|bool|null $page
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator|Run
     */
    public static function searchRuns(int|null|string $id = null,mixed $date = null, bool $last = false, int|bool|null $page = 1,int|null $perPage = 10)
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

        $page = max($page , 1);
        $perPage = min($perPage , 50);
        $perPage = max($perPage , 10);
        return Run::search($id ,$date , $last ,$page,$perPage);
    }
}
