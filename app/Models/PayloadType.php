<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayloadType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected const TYPES = [
        1 => 'LIQUID',
        2 => 'SOLID',
        3 => 'REFRIGERATED'
    ];

    public function payloadRequests()
    {
        return $this->hasMany(PayloadRequest::class);
    }

    public function enterPortRequests()
    {
        return $this->hasMany(PortRequest::class);
    }

    public static function getTypes() {

        return self::TYPES;
    }
}
