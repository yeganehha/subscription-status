<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Services\CheckService;
use App\Services\PlatformsService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class RunsQuery extends Query
{
    protected $attributes = [
        'name' => 'roundsCheck',
        'description' => 'Get List of all Platform(with search)'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Run');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'description' => 'Id of Round.',
                'type' => Type::id()
            ],
            'date' => [
                'name' => 'date',
                'description' => 'search special Day.',
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
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CheckService::searchRuns($args['id'] ?? null , $args['date'] ?? null, false,$args['status'] ?? null ,$args['page'] ?? 1, $args['limit'] ?? 10);
    }
}
