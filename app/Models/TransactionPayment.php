<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    const MODE_PAYPAL = 1;

    /**
     * @TODO create a custom Json cast for paypal_response
     */
    protected $appends = [
        'paypal_response'
    ];

    protected $hidden = [
        'paypal_response_json'
    ];

    protected $fillable = [
        'mode', 'paypal_order_id', 'paypal_response_json'
    ];

    use HasFactory;

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }

    public function getPaypalResponseAttribute()
    {
        return json_decode($this->paypal_response_json);
    }
}
