<?php

namespace App\Http\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class TransactionUpdateRequest extends FormRequest
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
            'buyer' => [
                'sometimes', 'nullable', 'integer', 'exists:users,id'
            ],

            'amount' => [
                'sometimes', 'numeric', 'min:200'
            ]
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->buyer) {
            try {
                $this->merge([
                    'buyer' => Hashids::decode($this->buyer)[0]
                ]);
            } catch (Exception $e) {}
        }
    }
}
