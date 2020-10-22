<?php

namespace Tests\Feature;

use App\Models\TransactionPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentReadMultipleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUnAuthorizedFetchAllShouldFail()
    {
        $response = $this->getJson('api/v1/transactions');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ], true);
    }

    public function testAuthorizedFetchAllWithoutParams()
    {
        $transaction = TransactionPayment::factory()->count(20)->create();

        $response = $this->getJson('api/v1/transaction/payments');
    }
}
