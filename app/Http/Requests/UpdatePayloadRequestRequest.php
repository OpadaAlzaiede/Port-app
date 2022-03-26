<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePayloadRequestRequest extends FormRequest
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
            'process_type_id' => 'exists:process_types,id',
            'payload_type_id' => 'exists:payload_types,id',
            'items' => 'array',
            'items.*.name' => 'required',
            'items.*.amount' => 'required'
        ];
    }
}
