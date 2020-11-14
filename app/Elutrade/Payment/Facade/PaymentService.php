<?php
namespace App\Elutrade\Payment\Facade;

use App\Elutrade\Payment\Payment;
use Illuminate\Support\Facades\Facade;

class PaymentService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Payment::class;
    }
}
