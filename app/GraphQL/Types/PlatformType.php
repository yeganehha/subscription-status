<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Platform;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PlatformType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Platform',
        'description' => 'application works on this platform.',
        'model' => Platform::class
    ];

    public function fields(): array
    {
        return Platform::GraphQLType();
    }
}
