<?php

namespace App\Models;

use App\Platforms\PlatformInterface;
use App\Services\AppsService;
use App\Services\PlatformsService;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Rebing\GraphQL\Support\Facades\GraphQL;

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

    public static function GraphQLType(): array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => 'The auto increment Platform Id.'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of Platform.'
            ],
            'apps' => [
                'type'          => GraphQL::paginate('Application'),
                'description'   => 'A list of application supported by the platform',
                'args'          => [
                    'id' => [
                        'name' => 'id',
                        'description' => 'Id of Application.',
                        'type' => Type::id()
                    ],
                    'uid' => [
                        'name' => 'uid',
                        'description' => 'UId of Application.',
                        'type' => Type::string()
                    ],
                    'name' => [
                        'name' => 'name',
                        'description' => 'search special name.',
                        'type' => Type::string()
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
                    return AppsService::search(
                    $args['id'] ?? null ,
                    $args['uid'] ?? null ,
                    $args['name'] ?? null,
                    $args['status'] ?? null,
                    $root->id,$args['page'] ?? 1 , $args['limit'] ?? 10);
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
     * return Provider of platform
     *
     * @return PlatformInterface
     */
    protected function provider_object(): PlatformInterface
    {
        $provider = $this->provider;
        if ( PlatformsService::isValidProvider($provider) )
            return  new ($provider)();
        throw new InvalidArgumentException("Platform [{$provider}] not found.");
    }

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
     *
     * @throws ModelNotFoundException
     */
    public static function findById(int $id) : Model
    {
        return self::query()->findOrFail($id);
    }

    public function apps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(App::class);
    }

    /**
     * search and return active platforms
     * @param int|null $id
     * @param string|null $name
     * @param int|bool|null $page
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator
     */
    public static function getActivePlatforms(int|null $id = null,string|null $name = null,int|bool|null $page,int|null $perPage = null):Collection|LengthAwarePaginator
    {
        $result = self::query()
            ->when($id , function ($query) use ($id){
                $query->where('id' , $id);
            })
            ->when($name , function ($query) use ($name){
                $query->where('name' , 'Like' , '%'. $name.'%');
            })->latest();
        if ( $page === false )
            return  $result->get();
        return $result->paginate($perPage,['*'],'page',$page);
    }
}
