<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionCreateTest extends TestCase
{
    use RefreshDatabase;

    public function testNonAuthenticatedCreateShouldFail()
    {
        $response = $this->postJson('api/v1/transactions', []);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ], true);
    }


    public function testMissingAll()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('api/v1/transactions', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'buyer', 'amount'
        ]);
    }

    public function testNonNumericAmountShouldFail()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('api/v1/transactions', [
            'buyer' => null,
            'amount' => 'asd.xd'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'amount'
        ]);
    }

    public function testNumericAmountShouldProceed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('api/v1/transactions', [
            'buyer' => null,
            'amount' => 420.69
        ]);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
        $this->assertIsArray($decoded['transaction']);
    }

    public function testNumericAmountButStringShouldProceed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('api/v1/transactions', [
            'buyer' => null,
            'amount' => '420.69'
        ]);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
        $this->assertIsArray($decoded['transaction']);

        $transaction = Transaction::findOrFail($decoded['transaction']['id']);

        $this->assertEquals($transaction->amount, $decoded['transaction']['amount']);
    }

    public function testNullBuyerShouldProceed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('api/v1/transactions', [
            'buyer' => null,
            'amount' => 420.69
        ]);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
        $this->assertIsArray($decoded['transaction']);

        $this->assertEquals(null, $decoded['transaction']['buyer']);
    }

    public function testBuyerIdNotFoundShouldFail()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('api/v1/transactions', [
            'buyer' => 9999,
            'amount' => 420.69
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'buyer'
        ]);
    }

    public function testBuyerIdExistsShouldProceed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $buyer = User::factory()->create();

        $response = $this->postJson('api/v1/transactions', [
            'buyer' => $buyer->id,
            'amount' => 420.69
        ]);

        $response->assertSuccessful();
        $decoded = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transaction', $decoded);
        $this->assertIsArray($decoded['transaction']);

        $this->assertEquals($buyer->id, $decoded['transaction']['buyer']['id']);
    }
}
