<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EnterPortPierResource extends JsonResource
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
                'order' => $this->order,
                'enter_date' => $this->enter_date,
                'leave_date' => $this->leave_date,
                'enter_port_request' => $this->PortRequest,
                'pier' => $this->pier,
            ];
    }
}
