<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionIndexRequest extends FormRequest
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
        /* TODO Rules for filter */
        return [
            'page.size' => ['sometimes', 'integer'],
            'page.number' => ['sometimes', 'integer'],
            'include' => [
                'sometimes',
                'array',
                Rule::in([
                    'buyer', 'seller', 'payment'
                ])
            ],
            'sort' => [
                'sometimes',
                'array',
                Rule::in([
                    'created_at', '-created_at',
                    'updated_at', '-updated_at'
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

        if ($this->sort) {
            $this->merge([
                'sort' => explode(',', $this->sort)
            ]);
        }
    }
}
