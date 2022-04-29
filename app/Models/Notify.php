<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    use HasFactory;
    protected $table='request_notifications';

    protected const NOTIFIABLES = [
        1 => PayloadRequest::class,
        2 => PortRequest::class
    ];

    public static function getNotifiables() {

        return self::NOTIFIABLES;
    }
}
