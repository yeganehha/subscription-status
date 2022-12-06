<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Subscription;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SubscriptionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Subscription',
        'model' => Subscription::class
    ];

    public function fields(): array
    {
        return Subscription::GraphQLType();
    }
}
