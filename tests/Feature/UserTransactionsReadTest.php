<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class UserTransactionsReadTest extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    public function testUnAuthRequest()
    {
        $viewer = User::factory()->create();
        $this->requestUnAuth('api/v1/user/'. $viewer->hash_id .'/transactions');
    }

    public function testGetUserAsBuyer()
    {
        $viewer = User::factory()->create();

        Sanctum::actingAs(
            $viewer,
            ['*']
        );

        [$buyer] = $this->createTransactions(30);
        $http = $this->requestJsonApi('api/v1/user/'. $buyer->hash_id .'/transactions', [
            'as' => 'buyer'
        ]);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertNotEmpty($http['json']['data']);
        $this->assertArrayNotHasKey('buyer', $http['json']['data']);
        $this->assertArrayNotHasKey('payment', $http['json']['data']);
        $this->assertArrayNotHasKey('seller', $http['json']['data']);
    }

    public function testGetUserAsSeller()
    {
        $viewer = User::factory()->create();

        Sanctum::actingAs(
            $viewer,
            ['*']
        );

        [$buyer, $seller] = $this->createTransactions(30);
        $http = $this->requestJsonApi('api/v1/user/' . $seller->hash_id . '/transactions', [
            'as' => 'seller'
        ]);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertNotEmpty($http['json']['data']);
        $this->assertArrayNotHasKey('buyer', $http['json']['data']);
        $this->assertArrayNotHasKey('payment', $http['json']['data']);
        $this->assertArrayNotHasKey('seller', $http['json']['data']);
    }

    public function testIncorrectParamAsShouldFail()
    {
        $viewer = User::factory()->create();

        Sanctum::actingAs($viewer, ['*']);

        [$buyer, $seller, $transactions] = $this->createTransactions(30);
        $http = $this->requestJsonApi('api/v1/user/'. $seller->hash_id .'/transactions', [
            'as' => 'non-existent'
        ]);

        $http['response']->assertStatus(422);
        $http['response']->assertJsonValidationErrors([
            'as'
        ]);
    }

    public function testGetUserAsSellerWithInclude()
    {
        $viewer = User::factory()->create();
        Sanctum::actingAs($viewer, ['*']);

        [$buyer] = $this->createTransactions(30);
        $http = $this->requestJsonApi('api/v1/user/'. $buyer->hash_id .'/transactions', [
            'as' => 'seller',
            'include' => 'payment,buyer,seller'
        ]);

        $http['response']->assertSuccessful();
        foreach ($http['json']['data'] as $data) {
            $this->assertArrayHasKey('payment', $data);
            $this->assertArrayHasKey('buyer', $data);
            $this->assertArrayHasKey('seller', $data);
        }
    }
}
