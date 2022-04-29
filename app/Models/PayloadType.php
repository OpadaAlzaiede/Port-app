<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PayloadType extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    public function payloadRequests()
    {
        return $this->hasMany(PayloadRequest::class);
    }

    public function enterPortRequests()
    {
        return $this->hasMany(PortRequest::class);
    }
}
