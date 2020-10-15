<?php

namespace App\Payments;

use App\Payments\Payment;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class Paypal extends Payment
{
    public static function client()
    {
        return new PayPalHttpClient(self::environment());
    }

    private static function environment()
    {
        return env('PAYPAL_ENV', 'sandbox') === 'sandbox' ?
            new SandboxEnvironment(env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET')) :
            new ProductionEnvironment(env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET'));
    }

    public static function createOrder(Transaction $transaction, $logErrorResponse = true)
    {
        self::checkTransaction($transaction, TransactionPayment::MODE_PAYPAL);

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.return_url'),
                'cancel_url' => route('paypal.cancel_url')
            ],

            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'PHP',
                        'value' => round($transaction->amount, 2)
                    ],
                    'description' => "Payment for transaction (#{$transaction->id}) - {env('APP_NAME')}.COM",
                    'invoice_id' => "{$transaction->id}-{$transaction->buyer_user_id}"
                ]
            ]
        ];

        $response = self::client()->execute($request);

        if ($response->statusCode === 200 || $response->statusCode === 201) {
            return $response;
        } else if ($logErrorResponse) {
            self::log(
                $transaction,
                "Paypal Response [createOrder][{$response->statusCode}]",
                json_encode($response->result)
            );
        }

        return false;
    }

    public static function getOrder(Transaction $transaction, $logErrorResponse = true)
    {
        self::checkTransaction($transaction, TransactionPayment::MODE_PAYPAL);

        $response = self::client()->execute(
            new OrdersGetRequest($transaction->paypal_order_id)
        );

        if ($response->statusCode === 200 || $response->statusCode === 201) {
            return $response;
        } else if ($logErrorResponse) {
            self::log(
                $transaction,
                "Paypal Response [getOrder][{$response->statusCode}]",
                json_encode($response->result)
            );
        }

        return false;
    }

    public static function captureOrder(Transaction $transaction, $logErrorResponse = true)
    {
        self::checkTransaction($transaction, TransactionPayment::MODE_PAYPAL);

        $response = self::client()->execute(
            new OrdersCaptureRequest($transaction->paypal_order_id)
        );

        if ($response->statusCode === 200 || $response->statusCode === 201) {
            return $response;
        } else if ($logErrorResponse) {
            self::log(
                $transaction,
                "Paypal Response [captureOrder][{$response->statusCode}]",
                json_encode($response->result)
            );
        }

        return false;
    }
}
