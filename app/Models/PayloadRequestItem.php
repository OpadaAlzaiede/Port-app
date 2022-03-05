<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayloadRequestItem extends Model
{
    use HasFactory;

    protected $table = 'payload_request_items';

    public function payloadRequest()
    {
        return $this->belongsTo(PayloadRequestItem::class, 'payload_request_id', 'id');
    }
}
