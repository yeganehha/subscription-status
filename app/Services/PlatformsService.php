<?php

namespace App\Services;

use App\Models\Platform;
use App\Platforms\PlatformInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;

class PlatformsService
{
    /**
     * check string is name of platform provider or not
     * @param string $name
     * @return bool
     */
    public static function isValidProvider(string $name) : bool
    {
        if (class_exists($name) and is_subclass_of($name, PlatformInterface::class))
            return true;
        return false;
    }


    /**
     * Get List Of active Providers.
     * @return Collection
     */
    public static function listProviders() : Collection
    {
        $providers = [];
        foreach ((new Finder)->in(app_path('Platforms\Providers'))->files() as $provider) {
            $provider = app()->getNamespace().str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($provider->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
                );

            if (self::isValidProvider($provider)) {
                $providers[] = $provider;
            }
        }
        return  collect($providers);
    }

    /**
     * Create new Platform.
     *
     * @param string $name
     * @param string $provider
     * @return Platform
     * @throws \Throwable
     */
    public static function insert(string $name, string $provider) : Platform
    {
        if ( ! self::isValidProvider($provider))
            throw new InvalidArgumentException("Platform [{$provider}] not found.");

        return Platform::insert($name , $provider);
    }

    /**
     * update platform data
     *
     * @param mixed $platform
     * @param string $name
     * @param string $provider
     * @return Platform
     * @throws \Throwable
     */
    public static function update(mixed $platform, string $name, string $provider): Platform
    {
        if ( is_int($platform) )
            $platform = self::findById($platform);

        if(! ( is_object($platform) and $platform instanceof Platform) )
            throw new InvalidArgumentException("Platform [".$platform::class."] not found.");

        if ( ! self::isValidProvider($provider))
            throw new InvalidArgumentException("Platform [{$provider}] not found.");

        return $platform->edit($name , $provider);
    }

    /**
     * Find Platform by id
     * @param int $id
     * @return Platform|null
     */
    public static function findById(int $id) : Platform|null
    {
        return Platform::findById($id);
    }

    public static function delete(Platform $platform) : bool
    {
        return $platform->delete();
    }

    /**
     * delete list of platforms
     * @param array $ids
     * @return bool
     */
    public static function deleteMultiply(array $ids) : bool
    {
        DB::beginTransaction();
        foreach ($ids as $id)
        {
            $platform = self::findById(intval($id));
            if ( $platform == null ) {
                DB::rollBack();
                throw new InvalidArgumentException("Platform [{$platform}] not found.");
            }
            self::delete($platform);
        }
        DB::commit();
        return true;
    }
}
