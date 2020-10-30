<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class TransactionLogsReadTest extends BaseTestCase
{
    use RefreshDatabase;

    public function testUnAuthRequest()
    {
        [$transaction, $logs] = $this->createLogs(40);
        $this->requestUnAuth('api/v1/transaction/' . $transaction->hash_id . '/logs');
    }

    public function testGetWithoutAnyParams()
    {
        [$transaction, $logs] = $this->createLogs(40);

        Sanctum::actingAs(
            $transaction->buyer,
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transaction/' . $transaction->hash_id . '/logs');

        $http['response']->assertSuccessful();
        $this->assertArrayHasKey('data', $http['json']);
        $this->assertNotEmpty($http['json']['data']);
    }

    public function testGetWithoutAnyParamsButSellerShouldReturnEmpty()
    {
        [$transaction, $logs] = $this->createLogs(10);

        Sanctum::actingAs(
            $transaction->seller,
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transaction/'. $transaction->hash_id .'/logs');

        $http['response']->assertSuccessful();
        $this->assertEmpty($http['json']['data']);
    }

    public function testGetWithIncludeParam()
    {
        [$transaction, $logs] = $this->createLogs(10);

        Sanctum::actingAs(
            $transaction->buyer,
            ['*']
        );

        $http = $this->requestJsonApi('api/v1/transaction/'. $transaction->hash_id .'/logs', [
            'include' => 'transaction'
        ]);

        $http['response']->assertSuccessful();
        $this->assertNotEmpty($http['json']);

        foreach ($http['json']['data'] as $log) {
            $this->assertArrayHasKey('transaction', $log);
        }
    }

    private function createLogs(int $count): array
    {
        $transaction = Transaction::factory()->create();
        $logs = TransactionLog::factory()->count($count)->make();

        foreach ($logs as $log) {
            $log->transaction_id = $transaction->id;
            $log->save();
        }

        return [$transaction, $logs];
    }
}
