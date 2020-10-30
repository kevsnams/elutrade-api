<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionShowRequest extends FormRequest
{
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
            'include' => [
                'sometimes',
                'array',
                Rule::in([
                    'seller', 'buyer', 'payment'
                ])
            ]
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->include) {
            $this->merge([
                'include' => explode(',', $this->include)
            ]);
        }
    }
}
