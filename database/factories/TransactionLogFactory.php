<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'transaction_id' => Transaction::factory(),
            'description' => $this->faker->text(30),
            'json_details' => json_encode([
                'example_data' => 'TEST',
                'foo' => 1,
                'bar' => 2,
                'baz' => [
                    'qux', 'idk', 'wtf'
                ]
            ])
        ];
    }
}
