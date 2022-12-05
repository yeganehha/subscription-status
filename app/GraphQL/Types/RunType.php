<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Run;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RunType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Run',
        'model' => Run::class
    ];

    public function fields(): array
    {
        return Run::GraphQLType();
    }
}
