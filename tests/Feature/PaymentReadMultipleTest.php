<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\BaseTestCase;

class PaymentReadMultipleTest extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    public function testUnAuthorizedFetchAllShouldFail()
    {
        $this->requestUnAuth('api/v1/transaction/payments');
    }

    public function testAuthorizedFetchAllWithoutParams()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        for ($i = 0; $i < 20; $i++) {
            $transaction = new Transaction();
            $transaction->buyer_user_id = $buyer->id;
            $transaction->seller_user_id = $seller->id;
            $transaction->amount = $this->faker->numberBetween(100, 999);
            $transaction->save();

            $payment = new TransactionPayment();
            $payment->transaction_id = $transaction->id;
            $payment->mode = TransactionPayment::MODE_PAYPAL;
            $payment->paypal_order_id = '123ABC456DEF';
            $payment->paypal_response_json = json_encode([
                'test_paypal' => 'test_response'
            ]);
            $payment->save();
        }

        Sanctum::actingAs(
            $buyer,
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transaction/payments');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']['data']);
        $this->assertNotEmpty($http['json']['data']);
        $this->assertEquals(config('json-api-paginate.default_size'), count($http['json']['data']));
        $this->assertEquals(TransactionPayment::ofBuyer($buyer->id)->count(), $http['json']['meta']['total']);
    }

    public function testFilter()
    {
        [$buyer, $seller, $transactions] = $this->createTransactionsWithPayments(10);

        Sanctum::actingAs($buyer, ['*']);

        $http = $this->requestJsonApi('api/v1/transaction/payments', [
            'include' => 'transaction',
            'filter' => [
                'mode' => TransactionPayment::MODE_PAYPAL
            ]
        ]);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertNotEmpty($http['json']['data']);

        foreach ($http['json']['data'] as $data) {
            $this->assertEquals(TransactionPayment::MODE_PAYPAL, $data['mode']);
        }
    }
}
