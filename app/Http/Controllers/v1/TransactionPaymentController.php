<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\TransactionPayment;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class TransactionPaymentController extends Controller
{
    private $indexPaginatePerPage = 10;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $request->validate([
            'per_page' => [
                'sometimes', 'numeric'
            ]
        ]);

        $payments = TransactionPayment::ofBuyer($request->user()->id)
            ->paginate($request->input('per_page', $this->indexPaginatePerPage));

        return [
            'success' => true,
            'transaction_payments' => $payments
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $payment = TransactionPayment::with(['transaction'])->find($id);

        return [
            'success' => true,
            'transaction_payment' => $payment
        ];
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
