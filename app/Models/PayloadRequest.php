<?php

namespace App\Models;

use App\constants\DataBaseConstants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

class PayloadRequest extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

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

    public function path()
    {

        return $this->belongsToMany(User::class)->withPivot('is_served');
    }

    public function refusals()
    {

        return $this->morphMany(Rejection::class, 'rejectable');
    }

    public function isDone()
    {

        return $this->status = DataBaseConstants::getStatusesArr()['DONE'];
    }

    public function createPath()
    {

        $this->path()->attach([Auth::id() => [
            'is_served' => DataBaseConstants::IS_SERVED_YES
        ]]);

        $officer = User::getLessLoadOfficer();

        $this->path()->attach([$officer->id => [
            'is_served' => DataBaseConstants::IS_SERVED_NO
        ]]);
    }
}
