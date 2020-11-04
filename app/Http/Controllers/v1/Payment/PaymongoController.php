<?php
namespace App\Http\Controllers\v1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Payments\Paymongo;
use Illuminate\Http\Request;

class PaymongoController extends Controller
{
    public function gcash(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ]
        ]);

        $transaction = Transaction::ofBuyer($request->user()->id)->findByHashidOrFail($request->transaction);
        $pmResponse = Paymongo::gcash($transaction)->send();
    }

    public function grabPay(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ]
        ]);

        $transaction = Transaction::ofBuyer($request->user()->id)->findByHashidOrFail($request->transaction);
        $pmResponse = Paymongo::gcash($transaction)->send();
    }
}
