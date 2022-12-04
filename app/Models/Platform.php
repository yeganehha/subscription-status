<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $name
 * @property string $provider
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider'
    ];


    /**
     * insert  new platform
     * @param string $name name of platform
     * @param string $provider provider class
     * @return self
     * @throws \Throwable
     */
    public static function insert(string $name, string $provider): self
    {
        $platform = new self();
        $platform->name = $name;
        $platform->provider = $provider;
        $platform->saveOrFail();
        return $platform;
    }

    /**
     * update Platform
     * @param string $name
     * @param string $provider
     * @return $this
     * @throws \Throwable
     */
    public function edit(string $name, string $provider): self
    {
        $this->name = $name;
        $this->provider = $provider;
        $this->saveOrFail();
        return $this;
    }

    /**
     * Find Platform
     * @param int $id
     * @return self
     */
    public static function findById(int $id) : Model
    {
        return self::query()->findOrFail($id);
    }

}
