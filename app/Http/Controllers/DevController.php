<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use App\Models\Transaction;
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

        $transactions = Transaction::paginate();

        return new ApiCollection($transactions);

        return [
            'success' => true,
            'transaction_payment' => $payment
        ];
    }
}
