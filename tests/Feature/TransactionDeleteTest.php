<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionDeleteTest extends TestCase
{
    public function testUnauthorizedDeleteShouldFail()
    {
        $transaction = Transaction::factory()->create();
        $response = $this->deleteJson('api/v1/transactions/'. $transaction->id);
        $decoded = $response->decodeResponseJson()->json();

        $response->assertStatus(401);
        $this->assertArrayHasKey('message', $decoded);
        $this->assertEquals('Unauthenticated.', $decoded['message']);
    }

    public function testAuthorizedSuccessfulDeleteShouldProceed()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $response = $this->deleteJson('api/v1/transactions/'. $transaction->id);
        $decoded = $response->decodeResponseJson()->json();

        $response->assertSuccessful();
        $this->assertArrayHasKey('success', $decoded);
        $this->assertEquals(true, $decoded['success']);
    }

    public function testMissingTransactionDeleteShouldFail()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson('api/v1/transactions/9999');
        $decoded = $response->decodeResponseJson()->json();

        $response->assertStatus(401);
        $this->assertArrayHasKey('message', $decoded);
        $this->assertEquals('Unauthenticated.', $decoded['message']);
    }

    public function testDifferentOwnerDeleteShouldFail()
    {
        $transaction = Transaction::factory()->create();
        $differentOwner = User::factory()->create();

        Sanctum::actingAs($differentOwner, ['*']);

        $response = $this->deleteJson('api/v1/transactions/'. $transaction->id);
        $decoded = $response->decodeResponseJson()->json();

        $response->assertStatus(401);
        $this->assertArrayHasKey('message', $decoded);
        $this->assertEquals('Unauthenticated.', $decoded['message']);
    }
}
