<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use Vinkla\Hashids\Facades\Hashids;

class TransactionPayment extends Model
{
    use HasFactory, HasHashid, HashidRouting;

    const MODE_PAYPAL = 1;

    protected $appends = [
        'paypal_response', 'hash_id'
    ];

    protected $hidden = [
        'paypal_response_json', 'transaction_id', 'id'
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

    public function getHashIdAttribute()
    {
        return $this->hashid();
    }

    public function setPaypalResponseAttribute($value)
    {
        $this->attributes['paypal_response_json'] = json_encode($value);
    }

    public function scopeOfBuyer(Builder $query, $id)
    {
        return $query->whereHas('transaction', function (Builder $childQuery) use ($id) {
            $id = is_string($id) ? Hashids::decode($id)[0] : $id;
            $childQuery->where('buyer_user_id', $id);
        });
    }
}
