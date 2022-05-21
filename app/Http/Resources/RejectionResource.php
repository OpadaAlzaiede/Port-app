<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RejectionResource extends JsonResource
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
                'reason' => $this->reason,
                'date' => $this->date,
                'rejectable_type' => $this->rejectable_type,
                'user' => $this->user,
            ];
    }
}
