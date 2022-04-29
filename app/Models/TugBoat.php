<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TugBoat extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'tugboats';

    protected $guarded = ['id'];

    public function shipTugboat()
    {
        return $this->hasOne(ShipTugboat::class);
    }
}
