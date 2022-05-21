<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePierYardRequest extends FormRequest
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
                'pier_id' => ['required', 'integer', Rule::exists('piers', 'id')],
                'yards' => ['required', 'array'],
                'yards.*.id' => ['required', 'integer', Rule::exists('yards', 'id')],
                'yards.*.distance' => ['required', 'numeric']
            ];
    }
}
