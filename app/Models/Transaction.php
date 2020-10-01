<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 0;
    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_CLOSED = 3;

    protected $hidden = [
        'seller_user_id', 'buyer_user_id'
    ];

    public function seller()
    {
        return $this->belongsTo('App\Models\User', 'seller_user_id');
    }

    public function buyer()
    {
        return $this->belongsTo('App\Models\User', 'buyer_user_id');
    }

    public function scopeOfSeller($query, $id)
    {
        return $query->where('seller_user_id', $id);
    }

    public function scopeOfBuyer($query, $id)
    {
        return $query->where('buyer_user_id', $id);
    }
}
