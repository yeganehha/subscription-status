<?php


namespace App\Enums;

use InvalidArgumentException;
use PhpParser\Node\Scalar\String_;

enum RunStatusEnum:string {
    case Finished = 'finished';
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

    /**
     * @param RunStatusEnum $enum
     * @return string
     */
    public static function toString(self $enum):string
    {
        switch ($enum) {
            case self::Finished :
                return 'Finished';
            case self::Pending :
                return 'Pending';
        }
    }
}
