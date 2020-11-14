<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use Vinkla\Hashids\Facades\Hashids;

class Transaction extends Model
{
    use HasFactory, HasHashid, HashidRouting;

    const STATUS_DRAFT = 0;
    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_CLOSED = 3;

    protected $hidden = [
        'seller_user_id', 'buyer_user_id', 'id'
    ];

    protected $fillable = [
        'buyer_user_id', 'seller_user_id', 'amount'
    ];

    protected $appends = [
        'hash_id'
    ];

    public function seller()
    {
        return $this->belongsTo('App\Models\User', 'seller_user_id');
    }

    public function buyer()
    {
        return $this->belongsTo('App\Models\User', 'buyer_user_id');
    }

    public function payment()
    {
        return $this->hasOne('App\Models\TransactionPayment');
    }

    public function logs()
    {
        return $this->hasMany('App\Models\TransactionLog');
    }

    public function scopeOfSeller(Builder $query, $id)
    {
        $id = is_string($id) ? (Hashids::decode($id)[0] ?? null) : $id;
        return $query->where('seller_user_id', $id);
    }

    public function scopeOfBuyer(Builder $query, $id)
    {
        $id = is_string($id) ? (Hashids::decode($id)[0] ?? null ) : $id;
        return $query->where('buyer_user_id', $id);
    }

    public function getHashIdAttribute()
    {
        return $this->hashid();
    }
}
