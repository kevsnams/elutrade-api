<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionPaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'transaction_id' => Transaction::factory(),
            'mode' => TransactionPayment::MODE_PAYPAL,
            'paypal_order_id' => '123ABC456DEF789GHI',
            'paypal_response_json' => json_encode(['example' => 'test only'])
        ];
    }
}
