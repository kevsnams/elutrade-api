<?php

namespace App\Http\Controllers;

use App\Http\Controllers\v1\TransactionController;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DevController extends Controller
{
    public function playground(Request $request)
    {
        // $buyer = User::factory()->create();
        // $seller = User::factory()->create();
        // $transactions = Transaction::factory()
        //     ->count(5)
        //     ->make();

        // foreach ($transactions as $transaction) {
        //     $transaction->buyer_user_id = $buyer->id;
        //     $transaction->seller_user_id = $seller->id;
        //     $transaction->amount = '9999';
        //     $transaction->save();
        // }

        return new ApiCollection(
            QueryBuilder::for(Transaction::class)
                ->ofSeller(78)
                ->allowedIncludes(['buyer', 'seller', 'payment'])
                ->defaultSort('-updated_at')
                ->allowedSorts('created_at', 'updated_at')
                ->allowedFilters([
                    AllowedFilter::scope('of_buyer')
                ])
                ->jsonPaginate()
        );
    }
}
