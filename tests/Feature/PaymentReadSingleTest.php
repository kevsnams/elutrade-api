<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class PaymentReadSingleTest extends BaseTestCase
{
    use RefreshDatabase;

    public function testSuccessfulRead()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs(
            $transaction->buyer,
            ['*']
        );

        $transaction->payment()->save(
            new TransactionPayment([
                'mode' => TransactionPayment::MODE_PAYPAL,
                'paypal_order_id' => 'ABC123DEF456GHI',
                'paypal_response_json' => json_encode([
                    'dummy' => 'response'
                ])
            ])
        );

        $transaction->refresh();
        $this->assertDatabaseHas('transaction_payments', ['id' => $transaction->payment->id]);

        $http = $this->requestJsonApi('api/v1/transaction/payments/'. $transaction->payment->id);
        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertEquals($transaction->payment->id, $http['json']['data']['id']);
    }

    public function testMissingPayment()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transaction/payments/90210');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertEmpty($http['json']['data']);
    }

    public function testNonAuthPayment()
    {
        $payment = TransactionPayment::factory()->create();
        $this->requestUnAuth('api/v1/transaction/payments/'. $payment->id);
    }

    public function testReadNotBuyerShouldReturnEmpty()
    {
        $notBuyer = User::factory()->create();
        $payment = TransactionPayment::factory()->create();

        Sanctum::actingAs(
            $notBuyer,
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transaction/payments/'. $payment->id);

        $http['response']->assertSuccessful();
        $this->assertEmpty($http['json']['data']);
    }
}
