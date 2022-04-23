<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rejection extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const MORPH = [
        1 => PayloadRequest::class,
        2 => PortRequest::class
    ];

    public function rejectable()
    {
        return $this->morphTo();
    }
}
