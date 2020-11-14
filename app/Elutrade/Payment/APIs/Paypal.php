<?php

namespace App\Elutrade\Payment\APIs;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Validation\ValidationException;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalHttp\HttpException;

class Paypal extends Base
{
    protected PayPalHttpClient $httpClient;
    protected $environment;

    public function __construct($app, Transaction $transaction, int $mode)
    {
        parent::__construct($app, $transaction, $mode);

        if ($app->config('elutrade.payments.api.paypal.environment') === 'live') {
            $this->environment = new ProductionEnvironment(
                $app->config('elutrade.payments.api.paypal.client_id'),
                $app->config('elutrade.payments.api.paypal.secret')
            );
        } else {
            $this->environment = new SandboxEnvironment(
                $app->config('elutrade.payments.api.paypal.client_id'),
                $app->config('elutrade.payments.api.paypal.secret')
            );
        }

        $this->httpClient = new PayPalHttpClient($this->environment);
    }

    public function create()
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'PHP',
                        'value' => round($this->transaction->amount, 2)
                    ],
                    'description' => "Payment for transaction [{$this->transaction->hash_id}] - " . $this->app->config('app.name') . ".COM",
                    'invoice_id' => "{$this->transaction->hash_id}-{$this->transaction->buyer_user_id}-" . uniqid()
                ]
            ]
        ];

        $this->log('Sent Paypal Order Request', $request->body);

        try {
            $this->setResponse(
                $this->httpClient->execute($request)
            );

            $this->log('Successful Paypal Order Request', $this->getResponse());
        } catch (HttpException $e) {
            $this->errorResponse($e);
            $this->log('Error Paypal Order Request', $this->getResponse());
        }

        return $this;
    }

    public function order()
    {
        try {
            $this->setResponse(
                $this->httpClient->execute(
                    new OrdersGetRequest($this->transaction->payment->paypal_order_id)
                )
            );
        } catch (HttpException $e) {
            $this->errorResponse($e);
        }

        return $this;
    }

    public function capture(string $orderId)
    {
        try {
            $this->setResponse(
                $this->httpClient->execute(
                    new OrdersCaptureRequest($orderId)
                )
            );

            $this->log('Successful Paypal Capture Request', $this->getResponse());

            $this->transaction->payment()->save(
                new TransactionPayment([
                    'mode' => $this->mode,
                    'paypal_order_id' => $this->getResponse()['result']['id'],
                    'paypal_response' => $this->getResponse()['result']
                ])
            );

            $this->log('Stored Transaction Payment', $this->transaction->payment->toArray());
        } catch (HttpException $e) {
            $this->errorResponse($e);

            $this->log(
                'Error Paypal Capture Request',
                array_merge(
                    $this->getResponse(),
                    ['order_id' => $orderId]
                )
            );
        }

        return $this;
    }

    public function cancel()
    {
        $this->log('Cancelled Paypal Payment');
        return $this;
    }

    private function errorResponse(HttpException $e) : void
    {
        $this->setResponse(
            ValidationException::withMessages([
                'paypal' => ['Paypal responded with HTTP Status ' . $e->statusCode]
            ])->errors()
        );
    }
}
