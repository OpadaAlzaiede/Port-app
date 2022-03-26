<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;

class StoreTugboatRequest extends FormRequest
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
            'tensile_strength' => 'required|numeric',
            'service_start_date' => 'required|date',
            'status' => 'required|numeric'
        ];
    }
}
