<?php

namespace App\Models;

use App\constants\DataBaseConstants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use OwenIt\Auditing\Contracts\Auditable;

class PortRequest extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    protected $table = 'enter_port_requests';

    public function processType()
    {
        return $this->belongsTo(ProcessType::class, 'process_type_id', 'id');
    }

    public function payloadType()
    {
        return $this->belongsTo(PayloadType::class, 'payload_type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portRequestItems()
    {
        return $this->hasMany(PortRequestItem::class, 'enter_port_request_id', 'id');
    }

    public function Path()
    {
        return $this->belongsToMany(User::class, 'user_enter_port_request', 'enter_port_request_id', 'user_id')->withPivot(['is_served']);
    }

    public function createPath()
    {

        $this->path()->attach(Auth::id(), [
            'is_served' => 1,
        ]);
        $officer = User::getUserByRoleName(Config::get('constants.roles.pier_officer_role'));
        $this->path()->attach([
            $officer->id => [
                'is_served' => 0,
            ]
        ]);

        $this->save();
    }

    public function pickPier($enterPortRequest)
    {
        $availablePiers = Pier::getInServicePiers();
        $pierWithAppropriateLength = Pier::scopeLength($availablePiers, $enterPortRequest);;
        $piers = Pier::matchPayloadType($pierWithAppropriateLength, $enterPortRequest->payload_type_id)->get();

        $result = Pier::getMinimumLoadingPiers($piers);


        foreach ($piers as $pier)
            if (!$pier->enterPortPiers()->exists())
                return $pier;


        if ($enterPortRequest->payload_type_id == DataBaseConstants::FOURTH) {
            return array_key_first($result);
        } else {

            if (count($result) > 1) {

                $i = 0;
                foreach ($result as $key => $value) {
                    if ($i > 1) break;
                    if ($i == 1) {
                        $secondPierId = $key;
                        $secondPierLeaveDate = new Carbon($value);
                        break;
                    }

                    $firstPierId = $key;
                    $firstPierLeaveDate = new Carbon($value);

                    $i++;
                }

                if ($firstPierLeaveDate->diffInMinutes($secondPierLeaveDate) > 30) {
                    $s = min($secondPierLeaveDate->getTimestamp(), $firstPierLeaveDate->getTimestamp());
                    if (Carbon::parse($s)->toDateTimeString() == $secondPierLeaveDate)
                        return $secondPierId;
                    return $firstPierId;
                }
            }

            return array_key_first($result);
        }


    }

}
