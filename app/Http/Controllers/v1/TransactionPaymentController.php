<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionPaymentCollectionRequest;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use App\Models\TransactionPayment;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionPaymentCollectionRequest $request, $id)
    {
        return new ApiCollection(
            QueryBuilder::for(TransactionPayment::class)
                ->ofBuyer($request->user()->id)
                ->allowedIncludes(['transaction'])
                ->defaultSort('-updated_at')
                ->allowedSorts('created_at', 'updated_at')
                ->jsonPaginate()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $payment = TransactionPayment::with(['transaction'])->ofBuyer($request->user()->id)->find($id);

        return new ApiResource($payment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        throw new AuthenticationException();
    }

    public function ofTransaction(Request $request, $id)
    {

    }
}
