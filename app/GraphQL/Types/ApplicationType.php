<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\App;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ApplicationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Application',
        'model' => App::class
    ];

    public function fields(): array
    {
        return App::GraphQLType();
    }
}
