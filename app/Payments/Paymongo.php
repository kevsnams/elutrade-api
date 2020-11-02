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
        if (!$transaction->buyer instanceof User) {
            throw new Exception('This transaction must have a buyer');
        }

        $source = new Source('gcash', self::createBasicAuth());
        /** TODO maybe modify transaction_payments to use a single unified JSON column */
    }


    private static function createBasicAuth()
    {
        return base64_decode(
            implode(':', [
                config('paymongo.keys.public'),
                config('paymongo.keys.private')
            ])
        );
    }
}
