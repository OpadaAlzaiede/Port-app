<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayloadRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function payloadType()
    {
        return $this->belongsTo(PayloadType::class);
    }

    public function processType()
    {
        return $this->belongsTo(ProcessType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payloadRequestItems()
    {
        return $this->hasMany(PayloadRequestItem::class);
    }
}
