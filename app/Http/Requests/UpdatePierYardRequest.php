<?php

namespace App\Http\Requests;

use App\Traits\JsonErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePierYardRequest extends FormRequest
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
                'pier_id' => ['integer', Rule::exists('piers', 'id')],
                'yard_id' => ['integer', Rule::exists('yards', 'id')],
                'distance' => ['integer'],
            ];
    }
}
