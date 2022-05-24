<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;

class StorePierRequest extends FormRequest
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
            'name' => 'required|string',
            'length' => 'required|numeric',
            'draft' => 'required|numeric',
            'code' => 'required|string',
            'type' => 'required|in:1,2',
            'payload_type_id' => 'required|exists:payload_types,id',
            'status' => 'required|in:1,2'
        ];
    }
}
