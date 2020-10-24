<?php

namespace Tests\Feature;

use App\Http\Controllers\v1\TransactionController;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\BaseTestCase;

class TransactionReadMultipleTest extends BaseTestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testUnAuthorizedFetchAllShouldFail()
    {
        User::factory()->has(Transaction::factory()->count(5))
            ->count(5)
            ->create();

        $this->requestUnAuth('api/v1/transactions');
    }

    public function testAuthorizedFetchAllWithoutParams()
    {
        $user = User::factory()->has(Transaction::factory()->count(30))->create();
        Sanctum::actingAs($user, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions');
        $this->assertCount(config('json-api-paginate.default_size'), $http['json']['data']);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertArrayHasKey('links', $http['json']);
        $this->assertArrayHasKey('meta', $http['json']);
        $this->assertEquals(true, $http['json']['success']);

        $transactions = Transaction::ofSeller($user->id)
            ->jsonPaginate(config('json-api-paginate.default_size'))
            ->toArray();

        $this->assertCount(count($transactions['data']), $http['json']['data']);
    }

    public function testAuthorizedFetchAllWithCustomPageSizeParam()
    {
        $user = User::factory()->has(Transaction::factory()->count(30))->create();

        Sanctum::actingAs($user, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions', [
            'page' => [
                'size' => 15
            ]
        ]);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertArrayHasKey('links', $http['json']);
        $this->assertArrayHasKey('meta', $http['json']);
        $this->assertEquals(true, $http['json']['success']);
        $this->assertEquals(30, $http['json']['meta']['total']);
        $this->assertEquals(2, $http['json']['meta']['last_page']);

        $transactions = Transaction::ofSeller($user->id)
            ->jsonPaginate(15)
            ->toArray();

        $this->assertCount(count($transactions['data']), $http['json']['data']);
    }

    public function testAuthorizedFetchAllWithCustomPageNumberParam()
    {
        $user = User::factory()->has(Transaction::factory()->count(30))->create();

        Sanctum::actingAs($user, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions', [
            'page' => [
                'number' => 2
            ]
        ]);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertArrayHasKey('links', $http['json']);
        $this->assertArrayHasKey('meta', $http['json']);
        $this->assertEquals(true, $http['json']['success']);
        $this->assertEquals(30, $http['json']['meta']['total']);
        $this->assertEquals(2, $http['json']['meta']['last_page']);

        $transactions = Transaction::ofSeller($user->id)
            ->paginate(15)
            ->toArray();

        $this->assertCount(count($transactions['data']), $http['json']['data']);
    }

    public function testAuthorizedFetchAllIncorrectWithParamShouldFail()
    {
        $user = User::factory()->has(Transaction::factory()->count(30))->create();
        Sanctum::actingAs($user, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions', [
            'page' => [
                'size' => 15
            ],
            'include' => 'incorrect'
        ]);

        $http['response']->assertStatus(422);
        $http['response']->assertJsonValidationErrors([
            'include'
        ]);
    }
}
