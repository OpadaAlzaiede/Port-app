<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Yard extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    public function piers()
    {
        return $this->belongsToMany(Pier::class, 'pier_yard')->withPivot(['distance']);
    }

    public function payloadType()
    {
        return $this->belongsTo(PayloadType::class, 'payload_type_id', 'id');
    }
}
