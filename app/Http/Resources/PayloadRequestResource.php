<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayloadRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'amount' => $this->amount,
            'shipping_policy_number' => $this->shipping_policy_number,
            'ship_number' => $this->ship_number,
            'date' => $this->date,
            'process_type' => $this->whenLoaded('processType'),
            'payload_type' => $this->whenLoaded('payloadType'),
            'user' => $this->whenLoaded('user'),
            'items' => $this->whenLoaded('payloadRequestItems'),
            'refusals' => RejectionResource::collection($this->whenLoaded('refusals')),
            'path' => $this->path,
        ];
    }
}
