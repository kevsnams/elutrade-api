<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransactionPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => [
                'required', 'integer', 'exists:App\Models\Transaction,id'
            ],

            'mode' => [
                'required', 'string', 'in:paypal'
            ]
        ]);

        $transaction = Transaction::find($request->transaction_id);

        #$payment = new TransactionPayment();
        #$payment->transaction_id = $transaction->id;

        if ($request->mode === 'paypal') {

            $paypalOrder = $this->paypalOrderCreate($transaction);
            return $paypalOrder;

            #$payment->mode = TransactionPayment::MODE_PAYPAL;
            #$payment->paypal_order_id = '';
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function getPaypalAuthToken()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US'
        ])->withBasicAuth(env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET'))
            ->asForm()
            ->post('https://api.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

        session(['paypal_access_token' => $response['access_token']]);
    }

    private function paypalOrderCreate(Transaction $transaction)
    {
        $request = function () use ($transaction) {
            return Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->withToken(session('paypal_access_token', ''))
                ->post('https://api.sandbox.paypal.com/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'PHP',
                                'value' => '420.69'
                            ],
                            'description' => "Payment for transaction (#{$transaction->id}) - {env('APP_NAME')}.COM",
                            'invoice_id' => "{$transaction->id}-{$transaction->buyer_user_id}"
                        ]
                    ]
                ]);
        };

        if ($request()->failed()) {
            $this->getPaypalAuthToken();
            return $request();
        }
    }
}
