<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int app_id
 * @property int run_id
 * @property StatusEnum status
 * @property App app
 * @property Run run
 */
class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'run_id',
        'status',
    ];

    public $timestamps = false;

    protected $casts = [
        'app_id' => 'int',
        'run_id' => 'int',
        'status' => StatusEnum::class
    ];

    public function run(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Run::class);
    }

    public function app(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(App::class);
    }
}
