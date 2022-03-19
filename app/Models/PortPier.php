<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortPier extends Model
{
    use HasFactory;

    protected $table = 'enter_port_pier';

    public function PortRequest()
    {
        return $this->belongsTo(PortRequest::class, 'enter_port_request_id', 'id');
    }

    public function Pier()
    {
        return $this->belongsTo(Pier::class, 'pier_id', 'id');
    }
}
