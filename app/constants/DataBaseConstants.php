<?php


namespace App\constants;


class DataBaseConstants
{
    const STATUSES = [
        "IN_PROGRESS" => 1,
        "DONE" => 2,
        "CANCELED" => 3
    ];
    const PIER_STATUSES = [
        "IN_SERVICE" => 1,
        "OUT_OF_SERVICE" => 2,
    ];

    const WAYS = [
        "FORWARD" => 1,
        "BACKWARD" => 2,
        "HOLDING" => 3
    ];

    const IS_SERVED_YES = 1;
    const IS_SERVED_NO = 0;

    //ROLES
    const USER_ROLE = 'user';
    const ADMIN_ROLE = 'admin';
    const OFFICER_ROLE = 'officer';

    //payload types
    const FIRST = 1;
    const SECOND = 2;
    const THIRD = 3;
    const FOURTH = 4;


    public static function getStatusesArr()
    {
        return self::STATUSES;
    }

    public static function getWaysArr()
    {
        return self::WAYS;
    }

    public static function getPierStatusArr()
    {
        return self::PIER_STATUSES;
    }
}
