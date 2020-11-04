<?php
namespace App\Payments;

use App\Models\Transaction;
use App\Models\User;
use App\Payments\Paymongo\Source;
use Exception;

class Paymongo extends Payment
{
    public static function gcash(Transaction $transaction)
    {
        self::checkTransaction($transaction);
        return (new Source('gcash', $transaction))->send();
    }

    public static function grabPay(Transaction $transaction)
    {
        self::checkTransaction($transaction);
        return (new Source('grab_pay', $transaction))->send();
    }

    private static function checkTransaction(Transaction $transaction) : void
    {
        if (is_null($transaction->buyer)) {
            throw new Exception('This transaction must have a buyer');
        }

        if (!is_null($transaction->payment)) {
            throw new Exception('This transaction already has a payment');
        }
    }
}
