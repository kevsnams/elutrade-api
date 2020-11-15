<?php
namespace App\Elutrade\Payment;

use App\Elutrade\Payment\APIs\Paymongo;
use App\Elutrade\Payment\APIs\Paypal;
use App\Models\Transaction;
use App\Models\TransactionPayment;

class Payment
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function paypal(Transaction $transaction)
    {
        return new Paypal(
            $this->app,
            $transaction,
            TransactionPayment::MODE_PAYPAL
        );
    }

    public function gcash(Transaction $transaction)
    {
        return (new Paymongo(
            $this->app,
            $transaction,
            TransactionPayment::MODE_PAYMONGO_GCASH
        ))->gcash();
    }
}
