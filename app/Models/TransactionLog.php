<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'description', 'json_details'
    ];

    protected $hidden = [
        'transaction_id'
    ];

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction');
    }
}
