<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugBoat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function shipTugboat()
    {
        return $this->hasOne(ShipTugboat::class);
    }
}
