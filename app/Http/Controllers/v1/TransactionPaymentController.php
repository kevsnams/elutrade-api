<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Payments\Paypal;
use Illuminate\Http\Request;

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

        $transaction = Transaction::ofBuyer($request->user()->id)->first($request->transaction_id);

        if ($request->mode === 'paypal') {
            if ($response = Paypal::createOrder($transaction)) {
                $payment = new TransactionPayment();

                $payment->mode = TransactionPayment::MODE_PAYPAL;
                $payment->paypal_order_id = $response['result']['id'];
                $payment->paypal_response_json = json_encode($response);
                $payment->transaction_id = $transaction->id;

                $payment->save();
                $payment->refresh();

                return [
                    'success' => true,
                    'transaction_payment' => $payment
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Unable to process PayPal for this transaction'
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Unable to process payment. Payment mode not found'
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $payment = TransactionPayment::with('transaction')
            ->ofBuyer($request->user()->id())
            ->first($id);

        return [
            'success' => true,
            'transaction_payment' => $payment
        ];
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
        // @TODO NEXT!
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
}
