<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\App;
use App\Models\Platform;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AppsService
{

    /**
     * Find application with UID
     * @param string $uid
     * @param int|App|null $except
     * @return bool
     */
    public static function isUidExist(string $uid , int|app|null $except = null ) : bool
    {
        if ( $except instanceof App)
            $except = $except->id;
        $otherApp = self::getAppByUID($uid) ;
        if ( $otherApp !== null and $otherApp->id !== $except )
            return true;
        return  false;
    }

    /**
     * Find application with UID
     * @param string $uid
     * @return App|null
     */
    public static function getAppByUID(string $uid) : App|null
    {
        return App::findByAttribute($uid , 'uid');
    }

    /**
     * Find application with ID
     * @param int|string $id
     * @return App|null
     */
    public static function getAppByID(int|string $id) : App|null
    {
        return App::findByAttribute( (int) $id);
    }

    /**
     * Create new App.
     *
     * @param string $uid
     * @param string|null $name
     * @param int|Platform $platform
     * @return App
     * @throws \Throwable
     */
    public static function insert(string $uid, string|null $name, int|Platform $platform) : App
    {
        if (self::isUidExist($uid))
            throw new InvalidArgumentException("UID [{$uid}] exist in database!");

        if ( is_int($platform) )
            $platform = PlatformsService::findById($platform);

        return App::insert($uid ,$name , ($platform)->id , StatusEnum::Pending);
    }


    /**
     * update app data
     *
     * @param int|App $app
     * @param string $uid
     * @param string|null $name
     * @param int|Platform $platform
     * @return App
     * @throws \Throwable
     */
    public static function update(int|App $app, string $uid, string|null $name, int|Platform $platform): App
    {
        if ( is_int($app) )
            $app = self::getAppByID($app);
        if ($app === null)
            throw new InvalidArgumentException("Application not found.");

        if (self::isUidExist($uid , $app))
            throw new InvalidArgumentException("UID [{$uid}] exist in database!");

        if ( is_int($platform) )
            $platform = PlatformsService::findById($platform);

        return ($app)->editInformation($uid , $name , ($platform)->id);
    }

    /**
     * update application status
     *
     * @param int|App $app
     * @param StatusEnum|string $status
     * @return App
     * @throws \Throwable
     */
    public static function status(int|App $app, StatusEnum|string $status): App
    {
        if ( is_int($app) )
            $app = self::getAppByID($app);
        if ($app === null)
            throw new InvalidArgumentException("Application not found.");

        if( is_string($status)){
            $status = StatusEnum::getFromString($status);
        }
        return ($app)->setStatus($status);
    }


    /**
     * delete application
     * @param App $app
     * @return bool
     */
    public static function deleteApp(App $app) : bool
    {
        return $app->delete() ?? false;
    }

    /**
     * delete list of applications
     * @param array $ids
     * @return bool
     */
    public static function deleteMultiply(array $ids) : bool
    {
        DB::beginTransaction();
        foreach ($ids as $id)
        {
            $app = self::getAppByID((int)$id);
            if ( $app == null ) {
                DB::rollBack();
                throw new InvalidArgumentException("Application [{$app}] not found.");
            }
            self::deleteApp($app);
        }
        DB::commit();
        return true;
    }


    /**
     * search application
     * @param int|string|null $id
     * @param string|null $uid
     * @param string|null $name
     * @param string|StatusEnum|null $status
     * @param int|Platform|string|null $platform
     * @param int|bool|null $page
     * @param int|null $perPage
     * @param array $with
     * @return Collection|LengthAwarePaginator
     */
    public static function search(int|null|string $id = null,null|string $uid = null ,string|null $name = null, string|StatusEnum|null $status = null, int|Platform|string|null $platform = null, int|bool|null $page,int|null $perPage = 10 , array $with = []): Collection|LengthAwarePaginator
    {
        if ( is_string($id) )
            $id = (int)$id;

        $platform_id = null ;
        if ( is_string($platform) or is_int($platform) )
            $platform_id = PlatformsService::findById($platform)->id;
        elseif ($platform instanceof Platform)
            $platform_id = $platform->id;


        if( is_string($status)){
            $status = StatusEnum::getFromString($status);
        }

        if ( $page !== false)
            $page = max($page , 1);
        $perPage = min($perPage , 50);
        $perPage = max($perPage , 10);
        return App::getApplications($id , $uid ,$name , $status,$platform_id ,$page,$perPage,$with);
    }
}
