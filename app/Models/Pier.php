<?php

namespace App\Models;

use App\constants\DataBaseConstants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pier extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    /**
     * @var int|mixed
     */
    private $status;

    public function enterPortPiers()
    {
        return $this->hasMany(PortPier::class, 'pier_id', 'id');
    }

    public function yards()
    {
        return $this->belongsToMany(Yard::class, 'pier_yard', 'pier_id', 'yard_id')->withPivot(['distance']);
    }

    public function enterPortRequests()
    {
        return $this->belongsToMany(PortRequest::class, 'enter_port_pier', 'pier_id', 'enter_port_request_id')->withPivot(['order', 'leave_date', 'enter_date']);
    }

    public function payloadType()
    {
        return $this->belongsTo(PayloadType::class, 'payload_type_id', 'id');
    }


    public static function getInServicePiers()
    {
        return self::where('status', DataBaseConstants::getPierStatusArr()["IN_SERVICE"]);
    }

    public static function getOtOfServicePiers()
    {
        return self::where('status', DataBaseConstants::getPierStatusArr()["OUT_OF_SERVICE"]);
    }

    public function activate()
    {
        $this->status = DataBaseConstants::getPierStatusArr()["IN_SERVICE"];
        return $this->save();
    }

    public function deactivate()
    {
        $this->status = DataBaseConstants::getPierStatusArr()["OUT_OF_SERVICE"];
        return $this->save();
    }

    public function isInService()
    {
        return $this->status === DataBaseConstants::getPierStatusArr()["IN_SERVICE"];
    }

    public static function scopeLength($piers, $length)
    {
        return $piers->where('length', '>=', $length);
    }

    public static function matchPayloadType($piers, $type)
    {
        return $piers->where('payload_type_id', '=', $type);
    }

    public function getLastLeaveDate()
    {
        return DB::table('enter_port_pier')->where('pier_id', $this->id)->orderByDesc('leave_date')->first();
    }

    public static function getMinimumLoadingPiers($piers)
    {
        $piers = $piers->get();
        $descLoadingPiers = [];

        foreach ($piers as $pier) {

            $model = $pier->getLastLeaveDate()->leave_date;
            $descLoadingPiers[$pier->id] = $model;
        }
        asort($descLoadingPiers);

        return $descLoadingPiers;
    }


}
