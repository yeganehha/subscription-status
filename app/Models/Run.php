<?php

namespace App\Models;

use App\Enums\RunStatusEnum;
use App\Services\AppsService;
use App\Services\CheckService;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;

/**
 * @property int $id
 * @property int expired_count
 * @property int waiting_task
 * @property RunStatusEnum status
 * @property string name
 * @property Collection subscriptions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Run extends Model
{
    use HasFactory;

    protected $fillable = [
        'expired_count',
        'waiting_task',
        'status'
    ];

    protected $casts = [
        'expired_count' => 'int',
        'waiting_task' => 'int',
        'status' => RunStatusEnum::class
    ];

    protected $appends = [
        'name',
    ];

    public static function GraphQLType(): array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => 'The auto increment Run Id.'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of run.'
            ],
            'expired_count' => [
                'type' => Type::int(),
                'description' => 'Number of application that get Expire Status in that\'s run.'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Status of round',
                'resolve' => function($root, $args) {
                    return RunStatusEnum::toString($root->status);
                }
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
                        null,
                        $args['status'] ?? null,
                        null,$root->id,$args['page'] ?? 1 , $args['limit'] ?? 10);
                },
            ],
            'run_at' => [
                'type' => Type::string(),
                'description' => 'The Date of Running check.',
                'resolve' => function($root, $args) {
                    return $root->created_at->format('Y-m-d');
                },
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The Date and time of Running check.'
            ]
        ];
    }

    /**
     * @param int|null $id
     * @param Carbon|null $date
     * @param bool $last
     * @param RunStatusEnum|null $status
     * @param int|bool|null $page
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator|Run
     */
    public static function search(int|null $id, Carbon|null $date, bool $last,RunStatusEnum|null $status = null, int|bool|null $page = false, int|null $perPage = null): Collection|LengthAwarePaginator|Run
    {
        $result = self::query()
            ->when($id , function ($query) use ($id){
                $query->where('id' , $id);
            })
            ->when($date , function ($query) use ($date){
                $query->whereDate('created_at' , $date);
            })
            ->when($status , function ($query) use ($status){
                $query->whereDate('status' , $status);
            })->latest();
        if ( $last )
            return $result->limit(1)->first();
        if ( $page === false )
            return  $result->get();
        return $result->paginate($perPage,['*'],'page',$page);
    }

    public function incrementExpiredApps(): int
    {
        self::query()->where('id',$this->id)
            ->increment('expired_count');
        $this->expired_count++;
        return  $this->expired_count;
    }

    public function decrementTask() :int
    {
        self::query()->where('id',$this->id)
            ->decrement('waiting_task');
        $this->waiting_task--;
        return  $this->waiting_task;
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function finish(): void
    {
        $this->status = RunStatusEnum::Finished;
        $this->saveOrFail();
    }

    /**
     * Get the user's first name.
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value , $attributes) => 'Run At '.Carbon::make($attributes['created_at'])->isoFormat('dddd Do MMMM, YYYY'),
        );
    }

    /**
     * insert  new Round of run
     * @param int $numberOFTask
     * @return self
     * @throws \Throwable
     */
    public static function insert(int $numberOFTask): self
    {
        $platform = new self();
        $platform->waiting_task = $numberOFTask;
        $platform->status = RunStatusEnum::Pending;
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

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
