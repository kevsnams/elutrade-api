<?php

namespace App\Http\Controllers\v1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Models\TransactionPayment;
use App\Payments\Paypal;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaypalController extends Controller
{
    public function postCreate(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ]
        ]);

        $transaction = Transaction::ofBuyer($request->user()->id)->findByHashid($request->transaction);
        $this->checkTransaction($transaction);

        $response = Paypal::createOrder($transaction);
        $this->checkPaypalResponse($response, $transaction, 'Failed creating Paypal order');

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'description' => 'Successfully created Paypal order',
            'json_details' => json_encode([
                'success' => true,
                'mode' => TransactionPayment::MODE_PAYPAL,
                'response_status' => $response->statusCode,
                'response_result' => $response->result
            ])
        ]);

        return [
            'success' => true,
            'paypal_order_id' => $response->result->id
        ];
    }

    public function postCapture(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ],
            'order_id' => [
                'required', 'string'
            ]
        ]);

        $transaction = Transaction::with('payment')
            ->ofBuyer($request->user()->id)
            ->findByHashid($request->transaction);

        $this->checkTransaction($transaction);

        $response = Paypal::captureOrder($request->order_id);
        $this->checkPaypalResponse($response, $transaction, 'Failed capturing payment from Paypal');

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'description' => 'Successfully paid using Paypal',
            'json_details' => json_encode([
                'success' => true,
                'mode' => TransactionPayment::MODE_PAYPAL,
                'response_status' => $response->statusCode,
                'response_result' => $response->result
            ])
        ]);

        $transaction->payment()->save(
            new TransactionPayment([
                'mode' => TransactionPayment::MODE_PAYPAL,
                'paypal_order_id' => $response->result->id,
                'paypal_response_json' => json_encode($response->result)
            ])
        );

        $transaction->refresh();

        return [
            'success' => true,
            'transaction' => $transaction
        ];
    }

    public function postCancel(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ]
        ]);

        $transaction = Transaction::ofBuyer($request->user()->id)->findByHashid($request->transaction);
        $this->checkTransaction($transaction);

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'description' => 'Cancelled Paypal payment',
            'json_details' => json_encode([
                'success' => false,
                'mode' => TransactionPayment::MODE_PAYPAL
            ])
        ]);

        return [
            'success' => true
        ];
    }

    protected function checkTransaction(Transaction $transaction)
    {
        if (!$transaction) {
            throw new AuthenticationException();
        }

        if ($transaction->payment) {
            throw ValidationException::withMessages([
                'transaction' => ['Transaction already has a payment']
            ]);
        }
    }

    protected function checkPaypalResponse($response, Transaction $transaction, String $onErrorLogMessage)
    {
        if (!$response) {
            throw ValidationException::withMessages([
                'paypal' => ['Failed communicating with Paypal servers at the moment. Please try again later']
            ]);
        }

        /**
         * If NOT success code
         */
        if (!($response->statusCode >= 200 && $response->statusCode < 300)) {
            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'description' => $onErrorLogMessage,
                'json_details' => json_encode([
                    'success' => false,
                    'mode' => TransactionPayment::MODE_PAYPAL,
                    'response_status' => $response->statusCode,
                    'response_result' => $response->result
                ])
            ]);

            throw ValidationException::withMessages([
                'paypal' => ['Paypal returned a HTTP Status '. $response->statusCode]
            ]);
        }
    }
}
