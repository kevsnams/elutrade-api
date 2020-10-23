<?php

namespace App\Http\Controllers;

use App\Models\TransactionPayment;
use Illuminate\Http\Request;

class DevController extends Controller
{
    public function playground(Request $request)
    {
        $payment = TransactionPayment::with('transaction')->find(2);

        $payment->paypal_response = [
            'test' => 'foobar'
        ];
        $payment->save();

        return [
            'success' => true,
            'transaction_payment' => $payment
        ];
    }
}
