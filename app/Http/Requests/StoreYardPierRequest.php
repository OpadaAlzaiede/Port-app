<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreYardPierRequest extends FormRequest
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
        return
            [
                'yard_id' => ['required', 'integer', Rule::exists('yards', 'id')],
                'piers' => ['required', 'array'],
                'piers.*.id' => ['required', 'integer', Rule::exists('piers', 'id')],
                'piers.*.distance' => ['required', 'string']
            ];
    }
}
