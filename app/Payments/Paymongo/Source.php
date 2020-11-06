<?php
namespace App\Payments\Paymongo;

use App\Models\Transaction;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Source
{
    const ENDPOINT = 'https://api.paymongo.com/v1/sources';

    protected array $data = [];
    protected string $type;
    protected Transaction $transaction;

    protected array $allowedTypes = [
        'gcash', 'grab_pay'
    ];

    public function __construct(string $type, Transaction $transaction)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new Exception('PAYMONGO: Source type `'. $type .'` is not allowed');
        }

        $this->type = $type;
        $this->transaction = $transaction;
        $this->initData();
    }

    public function send()
    {
        return Http::withBasicAuth(config('paymongo.keys.private'), '')
            ->post(Source::ENDPOINT, [
                'data' => $this->data
            ]);
    }

    private function initData() : void
    {
        $this->data = [
            'attributes' => [
                'type' => $this->type,
                'amount' => round($this->transaction->amount, 2) * 100,
                'currency' => 'PHP',
                'redirect' => [
                    'success' => config('paymongo.url.success'),
                    'failed' => config('paymongo.url.failed')
                ],

                'billing' => [
                    'name' => $this->transaction->buyer->full_name,
                    'email' => $this->transaction->buyer->email
                ]
            ]
        ];
    }
}
