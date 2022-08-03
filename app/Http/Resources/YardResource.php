<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'size' => $this->size,
            'payload_type' => $this->payloadType,
            'piers' => $this->piers,
            'capacity' => $this->capacity,
            'current_capacity' => $this->current_capacity,
            'status' => $this->status
        ];
    }
}
