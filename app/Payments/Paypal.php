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
use PayPalHttp\HttpException as PayPalHttpHttpException;

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

    public static function createOrder(Transaction $transaction)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'PHP',
                        'value' => round($transaction->amount, 2)
                    ],
                    'description' => "Payment for transaction [{$transaction->hash_id}] - ". env('APP_NAME') .".COM",
                    'invoice_id' => "{$transaction->hash_id}-{$transaction->buyer_user_id}-". uniqid()
                ]
            ]
        ];

        $response = null;

        try {
            $response = self::client()->execute($request);
        } catch (PayPalHttpHttpException $e) {}

        return $response;
    }

    public static function getOrder($paypalOrderId)
    {
        $response = null;

        try {
            $response = self::client()->execute(
                new OrdersGetRequest($paypalOrderId)
            );
        } catch (PayPalHttpClient $e) {}

        return $response;
    }

    public static function captureOrder($paypalOrderId)
    {

        $response = null;

        try {
            $response = self::client()->execute(
                new OrdersCaptureRequest($paypalOrderId)
            );
        } catch (PayPalHttpClient $e) {}

        return $response;
    }
}
