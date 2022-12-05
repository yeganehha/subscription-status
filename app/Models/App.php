<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Services\CheckService;
use App\Services\PlatformsService;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;

/**
 * @property int $id
 * @property string $uid
 * @property string $name
 * @property int $platform_id
 * @property Platform $platform
 * @property StatusEnum $status
 * @property Collection subscriptions
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

    public static function GraphQLType() :array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => 'The auto increment application Id.'
            ],
            'uid' => [
                'type' => Type::string(),
                'description' => 'The Unique ID of application.'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of application.'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Last Status of application',
                'resolve' => function($root, $args) {
                    return StatusEnum::toString($root->status);
                }
            ],
            'platform_id' => [
                'type' => Type::id(),
                'description' => 'The platform id of application.'
            ],
            'platform' => [
                'type'          => GraphQL::type('Platform'),
                'description'   => 'Platform of this application',
                'resolve' => function($root, $args) {
                    return PlatformsService::findById($root->platform_id);
                },
            ],
            'subscriptions' => [
                'type'          => GraphQL::paginate('Subscription'),
                'description'   => 'A subscriptions history of this run.',
                'args'          => [
                    'id' => [
                        'name' => 'id',
                        'description' => 'Id of Subscription.',
                        'type' => Type::id()
                    ],
                    'status' => [
                        'name' => 'status',
                        'description' => 'search special status.',
                        'type' => Type::string()
                    ],
                    'limit' => [
                        'name' => 'limit',
                        'description' => 'How much item show per each page.( between 10 & 50. default: 10)',
                        'type' => Type::int()
                    ],
                    'page' => [
                        'name' => 'page',
                        'description' => 'page number',
                        'type' => Type::int()
                    ]
                ],
                'resolve' => function($root, $args) {
                    return CheckService::searchSubscription(
                        $args['id'] ?? null,
                        $args['status'] ?? null,
                        $root->id,null,$args['page'] ?? 1 , $args['limit'] ?? 10);
                },
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The Date and time of last modification of Platform.'
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The Date and time of Platform created.'
            ]
        ];
    }


    /**
     * @param int|null $id
     * @param string|null $uid
     * @param string|null $name
     * @param StatusEnum|null $status
     * @param int|null $platform_id
     * @param int|bool|null $page
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator
     */
    public static function getApplications(int|null $id, string|null $uid, string|null $name, StatusEnum|null $status, int|null $platform_id, int|bool|null $page = false, int|null $perPage = null): Collection|LengthAwarePaginator
    {
        $result = self::query()
            ->when($id , function ($query) use ($id){
                $query->where('id' , $id);
            })
            ->when($uid , function ($query) use ($uid){
                $query->where('uid' , $uid);
            })
            ->when($name , function ($query) use ($name){
                $query->where('name' , 'Like' , '%'. $name.'%');
            })
            ->when($status , function ($query) use ($status){
                $query->where('status' , $status);
            })
            ->when($platform_id , function ($query) use ($platform_id){
                $query->where('platform_id' , $platform_id);
            })->latest();
        if ( $page === false )
            return  $result->get();
        return $result->paginate($perPage,['*'],'page',$page);
    }

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

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
