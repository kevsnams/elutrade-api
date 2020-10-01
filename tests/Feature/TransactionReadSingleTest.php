<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionReadSingleTest extends TestCase
{
    use RefreshDatabase;


    public function testUnauthorizedReadOnTransactionWithoutBuyerShouldProceed()
    {
        $transaction = Transaction::factory()->make();
        $transaction->buyer_user_id = null;
        $transaction->save();
        $transaction->refresh();

        $response = $this->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
        $this->assertIsArray($decoded['transaction']);
    }

    public function testUnauthorizedReadOnTransactionWithBuyerShouldReturnNull()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
        $this->assertNull($decoded['transaction']);
    }

    public function testAuthorizedReadOnTransactionWithoutBuyerShouldProceed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $transaction = Transaction::factory()->make();
        $transaction->buyer_user_id = null;
        $transaction->save();
        $transaction->refresh();

        $response = $this->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
        $this->assertIsArray($decoded['transaction']);
    }

    public function testAuthorizedReadOnTransactionWithDifferentBuyerShouldReturnNull()
    {
        $viewer = User::factory()->create();
        Sanctum::actingAs($viewer, ['*']);

        $transaction = Transaction::factory()->create();

        $response = $this->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
        $this->assertNull($decoded['transaction']);
    }
}
