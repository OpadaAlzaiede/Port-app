<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessType extends Model
{
    use HasFactory;

    protected $table = 'process_types';

    public function enterPortRequest()
    {
        return $this->hasMany(PortRequest::class);
    }

    public function payloadRequests()
    {
        return $this->hasMany(PayloadRequest::class);
    }
}
