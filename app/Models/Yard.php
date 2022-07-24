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
        return self::where('payload_type_id', $payloadTypeId);
    }

    public static function getYardByPierIdAndMatchYards($matchYards, $pierId)
    {
        return DB::table('pier_yard')->where('pier_id', $pierId->id)->whereIn('yard_id', $matchYards)
            ->orderBy('distance', 'asc')->get();
    }

    public function getInServiceYards($yards)
    {
        return $yards->where('status', 1);
    }

    public function scopeYardByCapacity($yards, $amount)
    {

        return $yards->where(function ($q) use ($amount) {

            $model = $q->first();

            $currentCapacity = $model->current_capacity;

            $capacity = $model->capacity;
            if ($capacity - $currentCapacity > $amount)
                return $model;

            return;
        });


    }

    public function getAppropriateYardByPierId($pierId, PortRequest $enterPortRequest)
    {
        $amount = $enterPortRequest->portRequestItems()->sum('amount');
        $appropriateYards = self::getYardByPayloadTypeId($enterPortRequest->payload_type_id);

        if ($enterPortRequest->process_type_id == 2){

            $appropriateYards = $this->scopeYardByCapacity($appropriateYards, $amount);

        }
        else
            $appropriateYards = $this->getInServiceYards($appropriateYards);



        return self::getYardByPierIdAndMatchYards($appropriateYards->get(), $pierId);

    }

    public function changeCapacity(PortRequest $enterPortRequest)
    {
        $amount = $enterPortRequest->portRequestItems()->sum('amount');
        if ($enterPortRequest->process_type_id == 2) {
            $this->current_capacity = $this->current_capacity + $amount;
            return $this->save();
        }
        $this->current_capacity = $this->current_capacity - $amount;
        return $this->save();

    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->status = 1;
        });
    }
}
