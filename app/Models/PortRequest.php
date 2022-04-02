<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'enter_port_requests';

    public function processType()
    {
        return $this->belongsTo(ProcessType::class, 'process_type_id', 'id');
    }

    public function payloadType()
    {
        return $this->belongsTo(PayloadType::class, 'payload_type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portRequestItems()
    {
        return $this->hasMany(PortRequestItem::class, 'enter_port_request_id', 'id');
    }
}
