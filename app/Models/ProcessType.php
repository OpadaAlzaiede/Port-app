<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProcessType extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    protected const TYPES = [
        1 => 'LOADING',
        2 => 'UNLOADING',
        3 => 'LOADING & UNLOADING',
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
