<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectedPayloadRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function rejection()
    {
        return $this->morphMany(Rejection::class, 'rejectable');
    }
}
