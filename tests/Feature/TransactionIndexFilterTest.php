<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class TransactionIndexFilterTest extends BaseTestCase
{
    use RefreshDatabase;

    public function testUnAuth()
    {
        $this->requestUnAuth('api/v1/transactions');
    }

    public function testFilterByBuyer()
    {
        [$buyer, $seller, $transactions] = $this->createTransactions(5);
        [$buyerOther, $sellerOther, $transactionsOther] = $this->createTransactions(8);

        Sanctum::actingAs($seller, ['*']);

        $http = $this->requestJsonApi('api/v1/transactions', [
            'include' => 'buyer',
            'filter' => [
                'of_buyer' => $buyer->hash_id
            ]
        ]);

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertNotEmpty($http['json']['data']);
        $this->assertCount(5, $http['json']['data']);

        foreach ($http['json']['data'] as $data) {
            $this->assertArrayHasKey('buyer', $data);
            $this->assertEquals($buyer->hash_id, $data['buyer']['hash_id']);
        }
    }
}
