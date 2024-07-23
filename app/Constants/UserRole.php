<?php

namespace App\Constants;

use App\Constants\BaseConstant;

class UserRole extends BaseConstant
{
    const ADMIN = 1;
    const SUPERADMIN = 2;
    const OPERATOR = 3;


    public static function labels()
    {
        return [
            static::ADMIN => 'Admin',
            static::SUPERADMIN => 'Super Admin',
            static::OPERATOR => 'Operator'
        ];
    }
}
