<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionUpdateTest extends BaseTestCase
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

        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id, [
            'buyer' => $buyer->hashid()
        ], 'PUT');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
    }

    public function testShouldNotUpdatedIfBuyerIsNotNull()
    {
        $transaction = Transaction::factory()->create();
        $buyer = User::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id, [
            'buyer' => $buyer->hashid()
        ], 'PUT');

        $http['response']->assertStatus(422);
        $http['response']->assertJsonValidationErrors([
            'buyer'
        ]);
    }

    public function testShouldNotUpdateAnyIfNotSeller()
    {
        $transaction = Transaction::factory()->create();
        $notSeller = User::factory()->create();
        $buyer = User::factory()->create();

        Sanctum::actingAs($notSeller, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id, [
            'buyer' => $buyer->hashid()
        ], 'PUT');

        $http['response']->assertStatus(401);
        $http['response']->assertJson([
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
        $http = $this->requestJsonApi('api/v1/transactions/' . $transaction->hash_id, [
            'buyer' => 9999
        ], 'PUT');

        $http['response']->assertStatus(422);
        $http['response']->assertJsonValidationErrors([
            'buyer'
        ]);
    }

    public function testMissingAllParameterShouldDoNothing()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions/' . $transaction->hash_id, [], 'PUT');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);

        foreach ($transaction->toArray() as $attribute => $value) {
            $this->assertEquals($value, $http['json']['data'][$attribute]);
        }
    }

    public function testCorrectAmountShouldProceed()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);
        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id, [
            'amount' => 420.69
        ], 'PUT');

        $http['response']->assertSuccessful();
        $transaction->refresh();

        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertEquals(420.69, $transaction->amount);
        $this->assertEquals(420.69, $http['json']['data']['amount']);
    }

    public function testIncorrectAmountShouldFail()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($transaction->seller, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id, [
            'amount' => '420.x9'
        ], 'PUT');

        $http['response']->assertStatus(422);
        $http['response']->assertJsonValidationErrors([
            'amount'
        ]);
    }

    public function testBuyerUpdatingShouldFail()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($transaction->buyer, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id, [
            'amount' => 420.69
        ], 'PUT');

        $http['response']->assertStatus(401);
        $http['response']->assertJson([
            'message' => 'Unauthenticated.'
        ], true);
    }
}
