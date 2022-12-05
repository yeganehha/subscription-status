<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @property int $id
 * @property string $uid
 * @property string $name
 * @property int $platform_id
 * @property Platform $platform
 * @property StatusEnum $status
 */
class App extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'name',
        'platform_id',
        'status'
    ];

    protected $casts = [
        'platform_id' => 'int',
        'status' => StatusEnum::class
    ];

    public function platform(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }


    /**
     * insert  new platform
     * @param string $uid
     * @param string|null $name name of platform
     * @param int $platform_id
     * @param StatusEnum $status
     * @return self
     * @throws \Throwable
     */
    public static function insert(string $uid, string|null $name, int $platform_id, StatusEnum $status): self
    {
        $platform = new self();
        $platform->uid = $uid;
        $platform->name = $name;
        $platform->platform_id = $platform_id;
        $platform->status = $status;
        $platform->saveOrFail();
        return $platform;
    }

    /**
     * update App
     * @param string $uid
     * @param string|null $name
     * @param int $platform_id
     * @return $this
     * @throws \Throwable
     */
    public function editInformation(string $uid, string|null $name, int $platform_id): self
    {
        $this->uid = $uid;
        $this->name = $name;
        $this->platform_id = $platform_id;
        $this->saveOrFail();
        return $this;
    }

    /**
     * update status of Application
     * @param StatusEnum $status
     * @return $this
     * @throws \Throwable
     */
    public function setStatus(StatusEnum $status): self
    {
        $this->status = $status;
        $this->saveOrFail();
        return $this;
    }

    /**
     * Find App
     * @param mixed $value
     * @param string $attribute
     * @return self|null
     */
    public static function findByAttribute(mixed $value, string $attribute = 'id') : self|null
    {
        return self::query()->where($attribute , $value)->first();
    }
}
