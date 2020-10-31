<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserShowRequest;
use App\Http\Requests\UserTransactionsRequest;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserShowRequest $request, $id)
    {
        return new ApiResource(
            User::with($request->input('include', []))
                ->findByHashid($id)
        );
    }

    /**
     * User -> Transactions
     *
     * Accepts:
     * - as: ['buyer', 'seller'] (default: 'seller')
     * - includes: 'payment,buyer' (default:none)
     *   A comma separated value of related models
     * - sort: ['updated_at', '-updated_at', 'created_at', '-created_at'] (default:-updated_at)
     *   Adding a prefix '-' (dash) means DESC
     **/
    public function transactions(UserTransactionsRequest $request, $id) : ApiCollection
    {
        $user = User::findByHashid($id);

        return new ApiCollection(
            QueryBuilder::for(Transaction::class)
                ->when($request->input('as') === 'buyer', function ($query) use ($user) {
                    return $query->ofBuyer($user->id);
                }, function ($query) use ($user) {
                    return $query->ofSeller($user->id);
                })
                ->allowedIncludes(['payment', 'buyer', 'seller'])
                ->defaultSort('-updated_at')
                ->allowedSorts(['updated_at', 'created_at', 'amount'])
                ->jsonPaginate()
        );
    }
}
