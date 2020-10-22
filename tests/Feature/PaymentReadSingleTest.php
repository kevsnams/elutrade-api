<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentReadSingleTest extends TestCase
{
    use RefreshDatabase;

    public function testSuccessfulRead()
    {
        $buyer = User::factory()->create();

        Sanctum::actingAs(
            $buyer,
            ['*']
        );

        $transaction = Transaction::factory()->create();
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

        $response = $this->getJson("/api/v1/transaction/payments/{$transaction->payment->id}");

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('transaction_payment', $decoded);
        $this->assertEquals($transaction->payment->id, $decoded['transaction_payment']['id']);
    }

    public function testMissingPayment()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->getJson('api/v1/transaction/payments/90210');
        $response->assertSuccessful();

        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('transaction_payment', $decoded);
        $this->assertEquals(null, $decoded['transaction_payment']);
    }

    public function testNonAuthPayment()
    {
        $response = $this->getJson('api/v1/transaction/payments/90210');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ], true);
    }
}
