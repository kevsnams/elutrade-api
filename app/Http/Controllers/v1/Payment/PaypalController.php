<?php

namespace App\Http\Controllers\v1\Payment;

use App\Elutrade\Payment\Facade\PaymentService;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaypalController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ]
        ]);

        return PaymentService::paypal(
            Transaction::ofBuyer($request->user()->id)->findByHashidOrFail($request->transaction)
        )->create()->toResponseJson();
    }

    public function capture(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ],
            'order_id' => [
                'required', 'string'
            ]
        ]);

        return PaymentService::paypal(
            Transaction::with('payment')
                ->ofBuyer($request->user()->id)
                ->findByHashidOrFail($request->transaction)
        )->capture()->toResponseJson();
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ]
        ]);

        return PaymentService::paypal(
            Transaction::ofBuyer($request->user()->id)->findByHashidOrFail($request->transaction)
        )->cancel()->toResponseJson();
    }
}
