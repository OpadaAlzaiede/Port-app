<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected const TYPES = [
        1 => 'LOADING',
        2 => 'UNLOADING',
    ];

    protected $table = 'process_types';

    public function enterPortRequest()
    {
        return $this->hasMany(PortRequest::class);
    }

    public function payloadRequests()
    {
        return $this->hasMany(PayloadRequest::class);
    }

    public static function getTypes() {

        return self::TYPES;
    }
}
