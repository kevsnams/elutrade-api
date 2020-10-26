<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionReadSingleTest extends BaseTestCase
{
    use RefreshDatabase;


    public function testUnauthorizedReadOnTransactionWithoutBuyerShouldProceed()
    {
        $transaction = Transaction::factory()->make();
        $transaction->buyer_user_id = null;
        $transaction->save();
        $transaction->refresh();

        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']['data']);
        $this->assertArrayNotHasKey('id', $http['json']['data'], 'Transaction should not have `id` visible');
    }

    public function testUnauthorizedReadOnTransactionWithBuyerShouldReturnEmptyArray()
    {
        $transaction = Transaction::factory()->create();
        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']['data']);
        $this->assertEmpty($http['json']['data']);
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

        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']['data']);
    }

    public function testAuthorizedReadOnTransactionWithDifferentBuyerShouldReturnEmptyArray()
    {
        $viewer = User::factory()->create();
        Sanctum::actingAs($viewer, ['*']);

        $transaction = Transaction::factory()->create();

        $http = $this->requestJsonApi('api/v1/transactions/'. $transaction->hash_id);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('success', $http['json']);
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertIsArray($http['json']['data']);
        $this->assertEmpty($http['json']['data']);
    }
}
