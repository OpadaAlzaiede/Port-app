<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipTugboat extends Model
{
    use HasFactory;

    protected $table = 'ship_tugboats';

    public function portRequest()
    {
        return $this->belongsTo(PortRequest::class, 'enter_port_request_id', 'id');
    }

    public function tugboat()
    {
        return $this->belongsTo(Tugboat::class, 'tugboat_id', 'id');
    }
}
