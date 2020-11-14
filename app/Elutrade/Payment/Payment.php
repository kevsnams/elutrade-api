<?php
namespace App\Elutrade\Payment;

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
}
