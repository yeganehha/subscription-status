<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Services\PlatformsService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class PlatformsQuery extends Query
{
    protected $attributes = [
        'name' => 'platforms',
        'description' => 'Get List of all Platform(with search)'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Platform');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'description' => 'Id of platform.',
                'type' => Type::id()
            ],
            'name' => [
                'name' => 'name',
                'description' => 'search special name.',
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
        return PlatformsService::searchPlatforms($args['id'] ?? null , $args['name'] ?? null, $args['page'] ?? 1, $args['limit'] ?? 10);
    }
}
