<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayloadRequestItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function payloadRequest()
    {
        return $this->belongsTo(PayloadRequest::class);
    }
}
