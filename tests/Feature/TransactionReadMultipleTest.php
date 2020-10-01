<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionReadMultipleTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testUnAuthorizedFetchAllShouldFail()
    {
        User::factory()->has(Transaction::factory()->count(5))
            ->count(5)
            ->create();

        $response = $this->getJson('api/v1/transactions');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ], true);
    }

    public function testAuthorizedFetchAllWithoutParamsShouldGetPerPageNumberOfItems()
    {
        $user = User::factory()->has(Transaction::factory()->count(30))->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('api/v1/transactions');

        $decoded = $response->decodeResponseJson()->json();

        $response->assertSuccessful();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transactions', $decoded);
        $this->assertArrayHasKey('data', $decoded['transactions']);
        $this->assertArrayHasKey('links', $decoded['transactions']);

        $this->assertCount(10, $decoded['transactions']['data']);

        $transactions = Transaction::ofSeller($user->id)
            ->paginate(10)
            ->toArray();

        $this->assertCount(count($transactions['data']), $decoded['transactions']['data']);
    }

    public function testAuthorizedFetchAllWithPerPageParam()
    {
        $user = User::factory()->has(Transaction::factory()->count(30))->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('api/v1/transactions?'. http_build_query([
            'per_page' => 15
        ]));

        $decoded = $response->decodeResponseJson()->json();

        $response->assertSuccessful();

        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('transactions', $decoded);
        $this->assertArrayHasKey('data', $decoded['transactions']);
        $this->assertArrayHasKey('links', $decoded['transactions']);

        $this->assertEquals(30, $decoded['transactions']['total']);
        $this->assertEquals(2, $decoded['transactions']['last_page']);

        $transactions = Transaction::ofSeller($user->id)
            ->paginate(15)
            ->toArray();

        $this->assertCount(count($transactions['data']), $decoded['transactions']['data']);
    }

    public function testAuthorizedFetchAllIncorrectWithParamShouldFail()
    {
        $user = User::factory()->has(Transaction::factory()->count(30))->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('api/v1/transactions?'. http_build_query([
            'per_page' => 15,
            'with' => ['incorrect']
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'with'
        ]);
    }
}
