<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Services\AppsService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class ApplicationQuery extends Query
{
    protected $attributes = [
        'name' => 'Application',
        'description' => 'Get single application.'
    ];

    public function type(): Type
    {
        return GraphQL::type('Application');
    }

    public function args(): array
    {
        return [
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
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if ( $args['id'] )
            return AppsService::getAppByID($args['id']);
        return AppsService::getAppByUID($args['uid'] ?? "");
    }
}
