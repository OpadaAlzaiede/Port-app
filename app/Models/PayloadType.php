<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayloadType extends Model
{
    use HasFactory;

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
