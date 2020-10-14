<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    const MODE_PAYPAL = 1;

    use HasFactory;

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }
}
