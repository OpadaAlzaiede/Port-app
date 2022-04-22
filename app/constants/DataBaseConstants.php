<?php


namespace App\constants;


class DataBaseConstants
{
    const STATUSES = [
        "IN_PROGRESS" => 1,
        "DONE" => 2,
        "CANCELED" => 3
    ];

    const WAYS = [
        "FORWARD" => 1,
        "BACKWARD" => 2,
        "HOLDING" => 3
    ];

    const IS_SERVED_YES = 1;
    const IS_SERVED_NO = 0;

    public static function getStatusesArr()
    {
        return self::STATUSES;
    }

    public static function getWaysArr()
    {
        return self::WAYS;
    }
}
