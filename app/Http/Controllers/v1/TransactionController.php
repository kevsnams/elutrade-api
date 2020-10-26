<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionCollectionRequest;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use App\Models\Transaction;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\QueryBuilder;

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
    public function index(TransactionCollectionRequest $request)
    {
        return new ApiCollection(
            QueryBuilder::for(Transaction::class)
                ->ofSeller($request->user()->id)
                ->allowedIncludes(['buyer', 'seller', 'payment'])
                ->defaultSort('-updated_at')
                ->allowedSorts('created_at', 'updated_at')
                ->jsonPaginate()
        );
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
                'required', 'numeric', 'min:200'
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

        return new ApiResource($transaction);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $transaction = Transaction::with('buyer', 'seller')->findByHashid($id);

        /* TODO Refactor this */
        if (Auth::check() && !is_null($request->buyer) && !in_array($request->user()->id, [$transaction->buyer->id, $transaction->seller->id])) {
            $transaction = null;
        } else if (!is_null($transaction->buyer)) {
            $transaction = null;
        }

        return new ApiResource($transaction);
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
        $transaction = Transaction::with(['buyer', 'seller'])
            ->ofSeller($request->user()->id)
            ->findByHashid($id);

        $request->validate([
            'buyer' => [
                'sometimes', 'nullable', 'integer', 'exists:users,id'
            ],

            'amount' => [
                'sometimes', 'numeric', 'min:200'
            ]
        ]);

        if (!$transaction) {
            throw new AuthenticationException();
        }

        $hasBuyer = !is_null($transaction->buyer);
        $filledBuyer = filled($request->buyer);

        if ($hasBuyer && $filledBuyer) {
            throw ValidationException::withMessages([
                'buyer' => ['This transaction already has a buyer']
            ]);
        }

        if (!$hasBuyer && $filledBuyer) {
            $transaction->buyer_user_id = $request->buyer;
        }

        if (filled($request->amount)) {
            $transaction->amount = round($request->amount, 2);
        }

        if ($transaction->isDirty()) {
            $transaction->save();
        }

        return new ApiResource($transaction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::ofSeller($request->user()->id)
            ->findByHashid($id);

        if (!$transaction) {
            throw new AuthenticationException();
        }

        $transaction->delete();

        return new ApiResource([]);
    }
}
