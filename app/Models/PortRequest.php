<?php

namespace App\Models;

use App\constants\DataBaseConstants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PortRequest extends Model
{
    use HasFactory;

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
        $officer = User::getUserByRoleName(DataBaseConstants::OFFICER_ROLE);
        $this->path()->attach([
            $officer->id => [
                'is_served' => 0,
            ]
        ]);

        $this->save();
    }
}
