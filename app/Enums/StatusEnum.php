<?php


namespace App\Enums;

use InvalidArgumentException;

enum StatusEnum:string {
    case Active = 'active';
    case Expired = 'expired';
    case Pending = 'pending';

    public static function getFromString(string $status):StatusEnum
    {
        $status = ucfirst(strtolower($status));
        $reflection = new \ReflectionEnum(self::class);
        if ( $reflection->hasConstant( $status ) ) {
            return $reflection->getConstant($status);
        } else
            throw new InvalidArgumentException("[{$status}] is not valid status!");
    }
}
