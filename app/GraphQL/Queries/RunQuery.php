<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Enums\RunStatusEnum;
use App\Services\CheckService;
use App\Services\PlatformsService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class RunQuery extends Query
{
    protected $attributes = [
        'name' => 'lastCheck',
        'description' => 'Last Finished Round of checking application\'s subscription.'
    ];

    public function type(): Type
    {
        return GraphQL::type('Run');
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CheckService::searchRuns(null ,  null, true ,RunStatusEnum::Finished, false);
    }
}
