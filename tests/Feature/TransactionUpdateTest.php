<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function testChangeBuyerToNonExistingBuyerIdWithExistingBuyer()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller, 'api')
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'buyer' => 999
            ]);

        $response->assertJsonValidationErrors([
            'buyer'
        ]);
    }

    public function testChangeBuyerToNullWithExistingBuyer()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller, 'api')
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'buyer' => null
            ]);

        $response->assertJsonValidationErrors([
            'buyer'
        ]);
    }

    public function testChangeBuyerWithExistingBuyer()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $buyer = User::factory()->create();
        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'buyer' => $buyer->id
            ]);

        $response->assertJsonMissingValidationErrors([
            'buyer'
        ]);
    }

    public function testChangeBuyerWithoutExistingBuyer()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $transaction = Transaction::factory()->make();
        $transaction->seller_user_id = $seller->id;
        $transaction->buyer_user_id = null;
        $transaction->save();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'buyer' => $buyer->id
            ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }

    public function testNonExistingStatusCode()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'status' => 9999
            ]);

        $response->assertJsonValidationErrors([
            'status'
        ]);
    }

    public function testSuccessfulStatusUpdate()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'status' => 1
            ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }

    public function testIncorrectAmount()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'amount' => '2x00'
            ]);

        $response->assertJsonValidationErrors([
            'amount'
        ]);
    }

    public function testStringButNumericAmount()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'amount' => '20330.00'
            ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }

    public function testDecimalNumericAmount()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'amount' => 20330.68
            ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }

    public function testIntNumericAmount()
    {
        $seller = User::factory()->has(Transaction::factory()->count(5))
            ->create();

        $transaction = Transaction::ofSeller($seller->id)->first();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'amount' => 20330
            ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }

    public function testMultipleAttributeUpdate()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $transaction = Transaction::factory()->make();
        $transaction->seller_user_id = $seller->id;
        $transaction->buyer_user_id = null;
        $transaction->save();

        $response = $this->actingAs($seller)
            ->putJson('api/v1/transactions/'. $transaction->id, [
                'buyer' => $buyer->id,
                'status' => 1,
                'amount' => 333.33
            ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'transaction' => []
        ]);
    }
}
