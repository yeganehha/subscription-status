<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Services\AppsService;
use App\Services\PlatformsService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ApplicationsQuery extends Query
{
    protected $attributes = [
        'name' => 'Applications',
        'description' => 'Get List of all Applications(with search)'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Application');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'description' => 'Id of platform.',
                'type' => Type::id()
            ],
            'uid' => [
                'name' => 'uid',
                'description' => 'search special unique id.',
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
            'platform_id' => [
                'name' => 'platform_id',
                'description' => 'search special platform with platform\'s id.',
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
        return AppsService::searchPlatforms(
                $args['id'] ?? null ,
                $args['uid'] ?? null ,
                $args['name'] ?? null,
                $args['status'] ?? null,
                $args['platform_id'] ?? null,
                $args['page'] ?? 1,
                $args['limit'] ?? 10);
    }
}
