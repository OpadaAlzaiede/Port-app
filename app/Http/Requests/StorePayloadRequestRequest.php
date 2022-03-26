<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;

class StorePayloadRequestRequest extends FormRequest
{
    use JsonErrors;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required',
            'process_type_id' => 'required|exists:process_types,id',
            'payload_type_id' => 'required|exists:payload_types,id',
            'shipping_policy_number' => 'required',
            'ship_number' => 'required',
            'items' => 'required|array',
            'items.*.name' => 'required',
            'items.*.amount' => 'required'
        ];
    }
}