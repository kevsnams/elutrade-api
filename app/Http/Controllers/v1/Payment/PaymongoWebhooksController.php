<?php

namespace App\Http\Controllers\v1\Payment;

use App\Http\Controllers\Controller;
use App\Models\TransactionLog;
use App\Models\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoWebhooksController extends Controller
{
    protected $endpointPayment = 'https://api.paymongo.com/v1/payments';

    public function sourceChargeable(Request $request)
    {
        $pmResponse = $request->input();
        $sourceId = $pmResponse['data']['attributes']['data']['id'];
        $pmAmount = $pmResponse['data']['attributes']['data']['attributes']['amount'] / 100;

        $payment = TransactionPayment::with(['transaction', 'transaction.buyer'])
            ->where('paymongo_source_id', $sourceId)->first();

        if (!$payment) {
            Log::channel('daily')->info('[PAYMONGO] source.chargeable can\'t find payment', [
                'source_id' => $sourceId,
                'amount' => $pmAmount
            ]);

            return;
        }

        $transactionAmount = round($payment->transaction->amount, 2) * 100;
        if ($transactionAmount !== $pmAmount) {
            Log::channel('daily')->info('[PAYMONGO] source.chargeable can\'t find payment', [
                'source_id' => $sourceId,
                'amount' => $pmAmount,
                'amount_transaction' => $transactionAmount,
                'transaction_id' => $payment->transaction->id,
                'payment_id' => $payment->id
            ]);

            return;
        }

        if (is_null($payment->transaction->buyer)) {
            TransactionLog::create([
                'transaction_id' => $payment->transaction->id,
                'description' => 'Payment transactio does not have a buyer. Cancelling payment.',
                'json_details' => json_encode([
                    'success' => false,
                    'mode' => $payment->mode,
                    'response' => $request->toArray()
                ])
            ]);

            return;
        }

        TransactionLog::create([
            'transaction_id' => $payment->transaction->id,
            'description' => 'Created paymongo payment',
            'json_details' => json_encode([
                'success' => true,
                'mode' => $payment->mode,
                'response' => $request->toArray()
            ])
        ]);

        $pmResponse = Http::withBasicAuth(config('paymongo.keys.private'), '')
            ->post($this->endpointPayment, [
                'data' => [
                    'attributes' => [
                        'amount' => round($payment->transaction->amount, 2) * 100,
                        'description' => config('app.name') .'.com payment for transaction: '. $payment->transaction->hashid(),
                        'currency' => 'PHP',
                        'source' => [
                            'id' => $payment->transaction->paymongo_source_id,
                            'type' => 'source'
                        ]
                    ]
                ]
            ]);

        if ($pmResponse->failed()) {
            TransactionLog::create([
                'transaction_id' => $payment->transaction->id,
                'description' => 'Failed trying to send payment to paymongo',
                'json_details' => json_encode([
                    'success' => false,
                    'response' => $pmResponse->json()
                ])
            ]);

            return;
        }

        $payment->paymongo_payment_id = $pmResponse['data']['id'];
        $payment->save();

        TransactionLog::create([
            'transaction_id' => $payment->transaction->id,
            'description' => 'Successfully sent paymongo payment',
            'json_details' => json_encode([
                'success' => true,
                'mode' => $payment->mode,
                'response' => $pmResponse->json(),
                'event_id' => $request->data->id
            ])
        ]);
    }
}
