<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEnterPortRequestRequest extends FormRequest
{
    use JsonErrors;

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return
            [
                'ship_name' => 'string',
                'ship_length' => 'numeric',
                'ship_draft_length' => 'numeric',
                'payload_weight' => 'numeric',
                'ship_weight' => 'numeric',
                'shipping_policy_number' => 'string',
                'process_type_id' => 'integer|exists:process_types,id',
                'payload_type_id' => 'integer|exists:payload_types,id',
            ];
    }
}
