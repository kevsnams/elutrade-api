<?php
namespace App\Elutrade\Transaction\Facade;

use App\Elutrade\Transaction\Transaction;
use Illuminate\Support\Facades\Facade;

class TransactionService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Transaction::class;
    }
}
