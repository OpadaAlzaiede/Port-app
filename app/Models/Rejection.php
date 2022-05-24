<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Rejection extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    const MORPH = [
        1 => PayloadRequest::class,
        2 => PortRequest::class
    ];

    public function rejectable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
