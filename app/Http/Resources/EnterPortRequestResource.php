<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EnterPortRequestResource extends JsonResource
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
                'ship_name' => $this->ship_name,
                'ship_length' => $this->ship_length,
                'ship_draft_length' => $this->ship_draft_length,
                'payload_weight' => $this->payload_weight,
                'ship_weight' => $this->ship_weight,
                'shipping_policy_number' => $this->shipping_policy_number,
                'status' => $this->status,
                'process_type' => $this->whenLoaded('processType'),
                'payload_type' => $this->whenLoaded('payloadType'),
                'user' => $this->whenLoaded('user'),
                'port_request_items' => $this->whenLoaded('portRequestItems'),
            ];
    }
}
