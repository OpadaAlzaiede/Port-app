<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PierYard extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'pier_yard';

    public function yard()
    {
        return $this->belongsTo(Yard::class);
    }

    public function pier()
    {
        return $this->belongsTo(Pier::class);
    }
}
