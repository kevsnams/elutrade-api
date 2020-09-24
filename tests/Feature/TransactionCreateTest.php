<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionCreateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testIncorrectAmountValue()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $response = $this->actingAs($seller, 'api')->postJson('api/v1/transactions', [
            'buyer' => $buyer->id,
            'amount' => '1x00'
        ]);

        $response->assertJsonValidationErrors([
            'amount'
        ]);
    }

    public function testNullBuyer()
    {
        $seller = User::factory()->create();
        $response = $this->actingAs($seller, 'api')->postJson('api/v1/transactions', [
            'buyer' => null,
            'amount' => 2000000.69
        ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);

        $newTransaction = $response->json('transaction');

        $this->assertDatabaseHas('transactions', [
            'id' => $newTransaction['id'],
            'seller_user_id' => $newTransaction['seller']['id'],
            'buyer_user_id' => null
        ]);
    }

    public function testIncorrectBuyerId()
    {
        $seller = User::factory()->create();
        $response = $this->actingAs($seller, 'api')->postJson('api/v1/transactions', [
            'buyer' => 69,
            'amount' => 200
        ]);

        $response->assertJsonValidationErrors([
            'buyer'
        ]);
    }

    public function testSuccessfulTransaction()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $response = $this->actingAs($seller, 'api')->postJson('api/v1/transactions', [
            'buyer' => $buyer->id,
            'amount' => $this->faker->numberBetween(10, 1000)
        ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);

        $newTransaction = $response->json('transaction');

        $this->assertDatabaseHas('transactions', [
            'id' => $newTransaction['id'],
            'seller_user_id' => $newTransaction['seller']['id'],
            'buyer_user_id' => $newTransaction['buyer']['id']
        ]);
    }
}
