<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BaseTestCase extends TestCase
{
    use WithFaker;

    public function requestJsonApi($url, $data = [], $verb = 'GET')
    {
        $response = $this->transformParams($url, $data, $verb);
        $json = $response->decodeResponseJson()->json();

        return compact('response', 'json');
    }

    public function requestUnAuth($url, $data = [], $verb = 'GET')
    {
        $response = $this->transformParams($url, $data, $verb);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ], true);
    }

    public function createTransactions(int $count): array
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $transactions = Transaction::factory()
            ->count($count)
            ->make();

        foreach ($transactions as $transaction) {
            $transaction->buyer_user_id = $buyer->id;
            $transaction->seller_user_id = $seller->id;
            $transaction->amount = $this->faker->numberBetween(100, 9999);
            $transaction->save();
        }

        return [$buyer, $seller, $transactions];
    }

    public function createTransactionsWithPayments(int $count) : array
    {
        [$buyer, $seller, $transactions] = $this->createTransactions($count);

        foreach ($transactions as $transaction) {
            $transaction->payment()->save(
                new TransactionPayment([
                    'transaction_id' => $transaction->id,
                    'mode' => TransactionPayment::MODE_PAYPAL,
                    'paypal_order_id' => 'ABC123DEF456GHI',
                    'paypal_response_json' => json_encode([
                        'test' => 'foo'
                    ])
                ])
            );
            $transaction->save();
            $transaction->refresh();
        }

        return [$buyer, $seller, $transactions];
    }

    private function transformParams($url, $data, $verb)
    {
        $params = [];
        $verb = strtolower($verb);

        if ($verb === 'get') {
            $params = [$url . (empty($data) ? '' : '?' . http_build_query($data))];
        } else {
            $params = [$url, $data];
        }

        return call_user_func_array([$this, $verb . 'Json'], $params);
    }
}
