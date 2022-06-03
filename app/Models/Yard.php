<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    public static function getYardByPayloadTypeId($payloadTypeId)
    {
        return self::where('payload_type_id', $payloadTypeId)->where('status', 1);
    }

    public static function getYardByPierIdAndMatchYards($matchYards, $pierId)
    {
        return DB::table('pier_yard')->where('pier_id', $pierId)->whereIn('yard_id', $matchYards)
            ->orderBy('distance', 'asc')->first();
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->status = 0;
        });
    }
}
