<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends \OwenIt\Auditing\Models\Audit implements \OwenIt\Auditing\Contracts\Audit
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
