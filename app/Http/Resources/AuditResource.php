<?php

namespace App\Http\Resources;

use App\Models\PayloadRequest;
use App\Models\PayloadRequestItem;
use App\Models\PayloadType;
use App\Models\PortPier;
use App\Models\PortRequest;
use App\Models\ProcessType;
use App\Models\Rejection;
use App\Models\ShipTugboat;
use App\Models\TugBoat;
use App\Models\Yard;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AuditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return
            [
                'id' => $this->id,
                'user' => new UserResource($this->user),
                'action' => $this->event,
                'auditable_type' => $this->auditable_type,
                'auditable' => $this->getResource($this->auditable_type, $this->auditable),
                'old_values' => $this->old_values,
                'new_values' => $this->new_values,
                'url' => $this->url,
                'ip_address' => $this->ip_address,
                'user_agent' => $this->user_agent,
                'date' => $this->created_at
            ];
    }

    private function getResource($auditable_type, $auditable)
    {

        switch ($auditable_type) {
            case PayloadRequest::class :
                return new PayloadRequestResource($auditable);
                break;
            case PayloadType::class :
                return new PayloadTypeResource($auditable);
                break;
            case PortRequest::class :
                return new EnterPortRequestResource($auditable);
                break;
            case ProcessType::class :
                return new ProcessTypeResource($auditable);
                break;
            case TugBoat::class :
                return new TugboatResource($auditable);
                break;
            case Yard::class :
                return new YardResource($auditable);
                break;
            case Rejection::class :
                return new RejectionResource($auditable);
                break;
            default:
                return null;
        }
    }
}
