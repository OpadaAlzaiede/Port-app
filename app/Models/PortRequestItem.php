<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortRequestItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'enter_port_request_items';

    public function PortRequest()
    {
        return $this->belongsTo(PortRequest::class, 'enter_port_request_id', 'id');
    }
}
