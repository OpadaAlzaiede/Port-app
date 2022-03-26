<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pier extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function enterPortPiers()
    {
        return $this->hasMany(PortPier::class);
    }

    public function yards()
    {
        return $this->belongsToMany(Yard::class, 'pier_yard')->withPivot(['distance']);
    }
}