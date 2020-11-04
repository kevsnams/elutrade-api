<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionCreateTest extends BaseTestCase
{
    use RefreshDatabase;

    public function testNonAuthenticatedCreateShouldFail()
    {
        $this->requestUnAuth('api/v1/transactions', [], 'POST');
    }


    public function testMissingAll()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transactions', [], 'POST');
        $http['response']->assertStatus(422);
        $http['response']->assertJsonValidationErrors([
            'buyer', 'amount'
        ]);
    }

    public function testNonNumericAmountShouldFail()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transactions', [
            'buyer' => null,
            'amount' => 'asd.xd'
        ], 'POST');

        $http['response']->assertStatus(422);
        $http['response']->assertJsonValidationErrors([
            'amount'
        ]);
    }

    public function testNumericAmountShouldProceed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $http = $this->requestJsonApi('api/v1/transactions', [
            'buyer' => null,
            'amount' => 420.69
        ], 'POST');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']['data']);
        $this->assertNotEmpty($http['json']['data']);
    }

    public function testNumericAmountButStringShouldProceed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $http = $this->requestJsonApi('api/v1/transactions', [
            'buyer' => null,
            'amount' => 420.69
        ], 'POST');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']);

        $transaction = Transaction::findByHashid($http['json']['data']['hash_id']);

        $this->assertEquals($transaction->amount, $http['json']['data']['amount']);
    }

    public function testNullBuyerShouldProceed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $http = $this->requestJsonApi('api/v1/transactions', [
            'buyer' => null,
            'amount' => 420.69
        ], 'POST');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']['data']);

        $transaction = Transaction::with(['buyer'])->findByHashid($http['json']['data']['hash_id']);

        $this->assertEquals(null, $transaction->buyer);
    }

    public function testBuyerIdNotFoundShouldFail()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transactions', [
            'buyer' => '9999',
            'amount' => 420.69
        ], 'POST');

        $http['response']->assertStatus(422);
        $http['response']->assertJsonValidationErrors([
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

        $http = $this->requestJsonApi('api/v1/transactions', [
            'buyer' => $buyer->hashid(),
            'amount' => 420.69
        ], 'POST');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']['data']);

        $transaction = Transaction::with(['buyer'])->findByHashid($http['json']['data']['hash_id']);
        $this->assertEquals($buyer->id, $transaction->buyer->id);
    }
}
