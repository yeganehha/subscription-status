<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Services\AppsService;
use App\Services\CheckService;
use App\Services\PlatformsService;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;

/**
 * @property int $id
 * @property int app_id
 * @property int run_id
 * @property StatusEnum last_status
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
        'last_status',
        'status',
    ];

    public $timestamps = false;

    protected $casts = [
        'app_id' => 'int',
        'run_id' => 'int',
        'last_status' => StatusEnum::class,
        'status' => StatusEnum::class
    ];

    public static function GraphQLType(): array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => 'The auto increment Subscription Id.'
            ],
            'app_id' => [
                'type' => Type::id(),
                'description' =>  'The applications id.'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Last Status of application',
                'resolve' => function($root, $args) {
                    return StatusEnum::toString($root->status);
                }
            ],
            'run_id' => [
                'type' => Type::id(),
                'description' => 'The round id of checking applications.'
            ],
            'application' => [
                'type'          => GraphQL::type('Application'),
                'description'   => 'Information of application.',
                'resolve' => function($root, $args) {
                    return AppsService::getAppByID($root->app_id);
                },
            ],
            'run' => [
                'type'          => GraphQL::type('Run'),
                'description'   => 'Information of round that\'s checked on',
                'resolve' => function($root, $args) {
                    return CheckService::getRunByID($root->run_id);
                },
            ]
        ];
    }

    /**
     * @param int|null $id
     * @param StatusEnum|null $lastStatus
     * @param StatusEnum|null $status
     * @param int|null $app_id
     * @param int|null $run_id
     * @param int|bool|null $page
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator
     */
    public static function search(int|null $id, StatusEnum|null $lastStatus, StatusEnum|null $status, int|null $app_id, int|null $run_id, int|bool|null $page = false, int|null $perPage = null): Collection|LengthAwarePaginator
    {
        $result = self::query()
            ->when($id , function ($query) use ($id){
                $query->where('id' , $id);
            })
            ->when($app_id , function ($query) use ($app_id){
                $query->where('app_id' , $app_id);
            })
            ->when($status , function ($query) use ($status){
                $query->where('status' , $status);
            })
            ->when($lastStatus , function ($query) use ($lastStatus){
                $query->where('last_status' , $lastStatus);
            })
            ->when($run_id , function ($query) use ($run_id){
                $query->where('run_id' , $run_id);
            })->orderByDesc('id');
        if ( $page === false )
            return  $result->get();
        return $result->paginate($perPage,['*'],'page',$page);
    }

    /**
     * @param int $app_id
     * @param int $run_id
     * @param StatusEnum $lastStatus
     * @param StatusEnum $status
     * @return Subscription
     * @throws \Throwable
     */
    public static function insert(int $app_id, int $run_id, StatusEnum $lastStatus, StatusEnum $status): self
    {
        $subscription = new self();
        $subscription->status = $status;
        $subscription->last_status = $lastStatus;
        $subscription->run_id = $run_id;
        $subscription->app_id = $app_id;
        $subscription->saveOrFail();
        return $subscription;
    }

    public function run(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Run::class);
    }

    public function app(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(App::class);
    }
}
