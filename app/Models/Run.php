<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int expired_count
 * @property string name
 * @property Collection subscriptions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Run extends Model
{
    use HasFactory;

    protected $fillable = [
        'expired_count'
    ];

    protected $casts = [
        'expired_count' => 'int'
    ];

    protected $appends = [
        'name',
    ];

    /**
     * Get the user's first name.
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value , $attributes) => 'Run At '.Carbon::make($attributes['created_at'])->isoFormat('dddd DDDo MMMM, YYYY'),
        );
    }

    /**
     * insert  new Round of run
     * @return self
     * @throws \Throwable
     */
    public static function insert(): self
    {
        $platform = new self();
        $platform->saveOrFail();
        return $platform;
    }

    /**
     * Find Run
     * @param int $id
     * @return Run
     * @throws ModelNotFoundException
     */
    public static function findById(int $id) : self
    {
        return self::query()->findOrFail($id);
    }
}
