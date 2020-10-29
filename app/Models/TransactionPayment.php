<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    use HasFactory;

    const MODE_PAYPAL = 1;

    /* TODO create a custom Json cast for paypal_response */
    protected $appends = [
        'paypal_response'
    ];

    protected $hidden = [
        'paypal_response_json', 'transaction_id'
    ];

    protected $fillable = [
        'mode', 'paypal_order_id', 'paypal_response'
    ];

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }

    public function getPaypalResponseAttribute()
    {
        return json_decode($this->attributes['paypal_response_json']);
    }

    public function setPaypalResponseAttribute($value)
    {
        $this->attributes['paypal_response_json'] = json_encode($value);
    }

    public function scopeOfBuyer(Builder $query, $id)
    {
        return $query->whereHas('transaction', function (Builder $childQuery) use ($id) {
            $childQuery->where('buyer_user_id', $id);
        });
    }
}
