<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yard extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function piers()
    {
        return $this->belongsToMany(Pier::class, 'pier_yard')->withPivot(['distance']);
    }
}
