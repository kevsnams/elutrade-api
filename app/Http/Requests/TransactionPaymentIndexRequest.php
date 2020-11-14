<?php

namespace App\Http\Requests;

use App\Models\TransactionPayment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionPaymentIndexRequest extends FormRequest
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
                    'transaction'
                ])
            ],
            'sort' => [
                'sometimes',
                'array',
                Rule::in([
                    'mode', '-mode',
                    'created_at', '-created_at',
                    'updated_at', '-updated_at'
                ])
            ],

            'filter' => [
                'sometimes',
                'array'
            ],

            'filter.mode' => [
                'sometimes',
                'integer',
                Rule::in([
                    TransactionPayment::modes()
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
