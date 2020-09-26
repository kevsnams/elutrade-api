<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionReadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NOTE: Fetching a single transaction should not have a middleware 'auth:api',
     * It can be publicly accessed IF buyer_user_id is NULL
     * If buyer_user_id IS NOT NULL, then it should only be visible to buyer/seller involved in the transaction
     */

    public function testBuyerFetching()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->actingAs($transaction->buyer, 'api')
            ->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }

    public function testDifferentBuyerFetching()
    {
        $transaction = Transaction::factory()->create();
        $buyer = User::factory()->create();

        $response = $this->actingAs($buyer, 'api')
            ->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => null
        ], true);
    }

    public function testSellerFetching()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->actingAs($transaction->seller, 'api')
            ->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }

    public function testDifferentSellerFetching()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->actingAs($transaction->seller, 'api')
            ->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => null
        ]);
    }

    public function testNonAuthUserFetchingWithBuyer()
    {
        $transaction = Transaction::factory()->create();

        $response = $this->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => null
        ], true);
    }

    public function testNonAuthUserFetchingWithoutBuyer()
    {
        $transaction = Transaction::factory()->make();
        $transaction->buyer_user_id = null;
        $transaction->save();
        $transaction->refresh();

        $response = $this->getJson('api/v1/transactions/'. $transaction->id);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }

    public function testNotFoundSingleFetch()
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller, 'api')
            ->getJson('api/v1/transactions/99');

        $response->assertJson([
            'success' => true,
            'transaction' => null
        ]);
    }

    public function testEmptyFetch()
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller, 'api')
            ->getJson('api/v1/transactions');

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'transactions' => []
        ]);
    }

    public function testFetchMany()
    {
        $seller = User::factory()
            ->has(Transaction::factory()->count(15))
            ->create();

        $anotherSellers = User::factory()
            ->has(Transaction::factory()->count(20))
            ->count(5)
            ->create();

        $response = $this->actingAs($seller, 'api')->getJson('api/v1/transactions');

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transactions' => []
        ]);

        $transactions = $response->json('transactions');
        $this->assertEquals($seller->transactions->count(), count($transactions));

        $this->assertDatabaseHas('transactions', [
            'seller_user_id' => $seller->id
        ]);
    }
}
