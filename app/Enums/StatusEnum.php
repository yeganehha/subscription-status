<?php


namespace App\Enums;

enum StatusEnum:string {
    case Active = 'active';
    case Expired = 'expired';
    case Pending = 'pending';
}
