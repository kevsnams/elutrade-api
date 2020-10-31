<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionIndexRequest;
use App\Http\Requests\TransactionLogsRequest;
use App\Http\Requests\TransactionShowRequest;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
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
    public function index(TransactionIndexRequest $request)
    {
        return new ApiCollection(
            QueryBuilder::for(Transaction::class)
                ->ofSeller($request->user()->id)
                ->allowedIncludes(['buyer', 'seller', 'payment'])
                ->defaultSort('-updated_at')
                ->allowedSorts('created_at', 'updated_at')
                ->allowedFilters([
                    AllowedFilter::scope('of_buyer')
                ])
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
    public function show(TransactionShowRequest $request, $id)
    {
        $transaction = Transaction::with($request->input('include', []))
            ->findByHashid($id);

        if (Auth::check() && !in_array($request->user()->id, [$transaction->buyer_user_id, $transaction->seller_user_id])) {
            $transaction = null;
        }

        if (!Auth::check() && !is_null($transaction->buyer)) {
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

    /**
     * Only buyers can access this
     **/
    public function logs(TransactionLogsRequest $request, $id)
    {
        return new ApiCollection(
            QueryBuilder::for(TransactionLog::whereHas(
                'transaction',
                function (Builder $query) use ($request) {
                    $query->ofBuyer($request->user()->id);
                })
            )->allowedIncludes(['transaction'])
            ->allowedSorts(['created_at', 'updated_at'])
            ->defaultSort('-updated_at')
            ->jsonPaginate()
        );
    }
}
