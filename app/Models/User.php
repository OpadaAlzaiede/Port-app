<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function ownedPayloadRequests()
    {
        return $this->hasMany(PayloadRequest::class);
    }

    public static function getUserByRoleName($role) {

        return self::role($role)
                    ->first();
    }

    public function payloadRequests() {

        return $this->belongsToMany(PayloadRequest::class, 'payload_request_user')->withPivot('is_served');
    }

    public static function getLessLoadOfficer() {

        $officers =  self::role(Config::get('constants.roles.officer_role'))->get();

        $lessLoadOfficer = $officers[0];
        $lessLoad = $lessLoadOfficer->payloadRequests()->where('is_served', 0)->count();

        foreach($officers as $officer) {

            // Update to add PortRequests ..
            $numOfRequests = $officer->payloadRequests()->where('is_served', 0)->count();
            
            if($numOfRequests < $lessLoad) {
                $lessLoad = $numOfRequests;
                $lessLoadOfficer = $officer;
            }
        }

        return $lessLoadOfficer;

    }
}
