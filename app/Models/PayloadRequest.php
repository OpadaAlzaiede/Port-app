<?php

namespace App\Models;

use App\Models\User;
use App\constants\DataBaseConstants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function path() {

        return $this->belongsToMany(User::class)->withPivot('is_served');
    }

    public function refusals() {

        return $this->morphMany(Rejection::class, 'rejectable');
    }

    public function createPath() {

        $this->path()->attach([Auth::id() => [
            'is_served' => DataBaseConstants::IS_SERVED_YES
        ]]);

        $officer = User::getUserByRoleName(Config::get('constants.roles.officer_role'));
        
        $this->path()->attach([$officer->id => [
            'is_served' => DataBaseConstants::IS_SERVED_NO
        ]]);
    }
}
