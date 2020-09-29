<?php

namespace App\Http\Controllers\v1;

use App\Http\Resources\Transaction as TransactionResource;
use App\Http\Resources\Transactions as TransactionsResource;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transactions = Transaction::ofSeller($request->user()->id)->get();
        return new TransactionsResource($transactions);
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
            'buyer' => [
                'present',
                'nullable',
                'exists:users,id'
            ],

            'amount' => [
                'required', 'numeric', 'min:0'
            ]
        ]);

        $transaction = new Transaction();

        $transaction->seller_user_id = $request->user()->id;

        if (!is_null($request->buyer)) {
            $transaction->buyer_user_id = $request->buyer;
        }

        $transaction->amount = round($request->amount, 2);
        $transaction->save();
        $transaction->refresh();

        return new TransactionResource($transaction);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $transaction = Transaction::with('buyer', 'seller')->find($id);

        if (Auth::check() && !is_null($request->buyer) && !in_array($request->user()->id, [$transaction->buyer->id, $transaction->seller->id])) {
            $transaction = null;
        } else if (!is_null($transaction->buyer)) {
            $transaction = null;
        }

        return [
            'success' => true,
            'transaction' => $transaction
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
        $request->validate([
            'buyer' => [
                'sometimes',
                'nullable',
                'required',
                'exists:users,id'
            ],

            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    Transaction::STATUS_ACTIVE,
                    Transaction::STATUS_CLOSED,
                    Transaction::STATUS_DRAFT,
                    Transaction::STATUS_PENDING
                ])
            ],

            'amount' => [
                'sometimes',
                'required',
                'numeric'
            ]
        ]);

        $transaction = Transaction::ofSeller($request->user()->id)->first();

        // [ W I P ]
        if (is_null($transaction)) {
            return response()->json([
                'success' => false,
                'transaction' => []
            ], 404);
        }

        if (is_null($transaction->buyer) && $request->has('buyer')) {
            $transaction->buyer_user_id = $request->buyer;
        }

        if ($request->has('status')) {
            $transaction->status = $request->status;
        }

        if ($request->has('amount')) {
            $transaction->amount = round($request->amount, 2);
        }

        if ($transaction->isDirty()) {
            $transaction->save();
        }

        return new TransactionResource($transaction);
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
