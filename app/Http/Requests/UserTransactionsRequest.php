<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserTransactionsRequest extends FormRequest
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
            'page.size' => ['sometimes', 'integer'],
            'page.number' => ['sometimes', 'integer'],
            'include' => [
                'sometimes',
                'array',
                Rule::in([
                    'payment', 'buyer', 'seller'
                ])
            ],
            'as' => [
                'sometimes',
                'string',
                'regex:/(buyer|seller)/'
            ],
            'sort' => [
                'sometimes',
                'array',
                Rule::in([
                    'created_at', '-created_at',
                    'updated_at', '-updated_at',
                    'amount', '-amount'
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
