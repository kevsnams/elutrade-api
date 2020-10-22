<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DevController extends Controller
{
    public function playground(Request $request)
    {
        $transaction = Transaction::find(51);

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);

        return view('dev.playground', compact('transaction'));
    }
}
