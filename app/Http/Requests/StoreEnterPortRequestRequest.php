<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnterPortRequestRequest extends FormRequest
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
                'ship_name' => 'required|string',
                'ship_length' => 'required|numeric',
                'ship_draft_length' => 'required|numeric',
                'payload_weight' => 'required|numeric',
                'ship_weight' => 'required|numeric',
                'shipping_policy_number' => 'required|string',
                'process_type_id' => 'required|integer|exists:process_types,id',
                'payload_type_id' => 'required|integer|exists:payload_types,id',
            ];
    }
}
