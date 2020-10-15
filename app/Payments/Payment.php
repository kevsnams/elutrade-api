<?php
namespace App\Payments;

use App\Models\Transaction;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\TransactionLog;

class Payment
{
    public static function checkTransaction(Transaction $transaction, $expectedMode)
    {
        if (!$transaction) {
            throw new HttpException(500, 'Unable to process');
        }

        if ($transaction->mode !== $expectedMode) {
            throw new HttpException(500, 'Unable to process');
        }
    }

    public static function log(Transaction $transaction, $description, $json_details)
    {
        $log = new TransactionLog();
        $log->transaction_id = $transaction->id;
        $log->description = $description;
        $log->json_details = $json_details;
        $log->save();
    }
}
