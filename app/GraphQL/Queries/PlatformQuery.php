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

class PlatformQuery extends Query
{
    protected $attributes = [
        'name' => 'platform',
        'description' => 'Get single platform.'
    ];

    public function type(): Type
    {
        return GraphQL::type('Platform');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'description' => 'Id of platform.',
                'type' => Type::nonNull(Type::id())
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return PlatformsService::findById($args['id']);
    }
}
