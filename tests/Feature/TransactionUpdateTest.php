<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionUpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testShouldUpdateIfBuyerIsNull()
    {
        $transaction = Transaction::factory()->make();
        $transaction->buyer_user_id = null;
        $transaction->save();
        $transaction->refresh();

        $buyer = User::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $response = $this->putJson('api/v1/transactions/'. $transaction->id, [
            'buyer' => $buyer->id
        ]);

        $response->assertSuccessful();

        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
    }

    public function testShouldNotUpdatedIfBuyerIsNotNull()
    {
        $transaction = Transaction::factory()->create();
        $buyer = User::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $response = $this->putJson('api/v1/transactions/'. $transaction->id, [
            'buyer' => $buyer->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'buyer'
        ]);
    }

    public function testShouldNotUpdateAnyIfNotSeller()
    {
        $transaction = Transaction::factory()->create();

        $notSeller = User::factory()->create();
        $buyer = User::factory()->create();

        Sanctum::actingAs($notSeller, ['*']);

        $response = $this->putJson('api/v1/transactions/'. $transaction->id, [
            'buyer' => $buyer->id
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ], true);
    }

    public function testShouldFailIfBuyerIdDoesNotExist()
    {
        $transaction = Transaction::factory()->make();
        $transaction->buyer_user_id = null;
        $transaction->save();
        $transaction->refresh();

        Sanctum::actingAs($transaction->seller, ['*']);

        $response = $this->putJson('api/v1/transactions/'. $transaction->id, [
            'buyer' => '9999'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'buyer'
        ]);
    }

    public function testMissingAllParameterShouldDoNothing()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $response = $this->putJson('api/v1/transactions/'. $transaction->id, []);
        $decoded = $response->decodeResponseJson()->json();

        $response->assertSuccessful();
        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
    }

    public function testCorrectAmountShouldProceed()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $response = $this->putJson('api/v1/transactions/'. $transaction->id, [
            'amount' => 420.69
        ]);
        $decoded = $response->decodeResponseJson()->json();

        $response->assertSuccessful();
        $transaction->refresh();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);

        $this->assertEquals(420.69, $transaction->amount);
    }

    public function testIncorrectAmountShouldFail()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $response = $this->putJson('api/v1/transactions/'. $transaction->id, [
            'amount' => '420.x9'
        ]);
        $decoded = $response->decodeResponseJson()->json();

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'amount'
        ]);
    }
}
