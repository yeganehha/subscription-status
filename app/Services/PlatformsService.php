<?php

namespace App\Services;

use App\Models\Platform;
use App\Platforms\PlatformInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * @param int|Platform $platform
     * @param string $name
     * @param string $provider
     * @return Platform
     * @throws \Throwable
     */
    public static function update(int|Platform $platform, string $name, string $provider): Platform
    {
        if ( is_int($platform) )
            $platform = self::findById($platform);

        if ( ! self::isValidProvider($provider))
            throw new InvalidArgumentException("Platform [{$provider}] not found.");

        return ($platform)->edit($name , $provider);
    }

    /**
     * Find Platform by id
     * @param int $id
     * @return Platform
     * @throws ModelNotFoundException
     */
    public static function findById(int $id) : Platform
    {
        return Platform::findById($id);
    }

    public static function deletePlatform(Platform $platform) : bool
    {
        return $platform->delete() ?? false;
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
            $platform = self::findById((int)$id);
            if ( $platform == null ) {
                DB::rollBack();
                throw new InvalidArgumentException("Platform [{$platform}] not found.");
            }
            self::deletePlatform($platform);
        }
        DB::commit();
        return true;
    }


    /**
     * Return list of platforms
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function listPlatforms(): Collection
    {
        return Platform::getActivePlatforms();
    }
}
