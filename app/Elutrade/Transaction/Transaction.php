<?php
namespace App\Elutrade\Transaction;

use App\Models\Transaction as ModelsTransaction;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class Transaction
{
    public function __construct($app)
    {

    }

    public function create(User $user, array $data) : ModelsTransaction
    {
        return ModelsTransaction::create([
            'buyer_user_id' => $data['buyer'],
            'seller_user_id' => $user->id,
            'amount' => round($data['amount'], 2)
        ]);
    }

    public function update(ModelsTransaction $transaction = null, User $user, array $data) : ModelsTransaction
    {
        if (!$transaction) {
            throw new AuthenticationException();
        }

        $hasBuyer = !is_null($transaction->buyer);
        $filledBuyer = isset($data['buyer']) && filled($data['buyer']);

        if ($hasBuyer && $filledBuyer) {
            throw ValidationException::withMessages([
                'buyer' => ['This transaction already has a buyer']
            ]);
        }

        if (!$hasBuyer && $filledBuyer) {
            $transaction->buyer_user_id = $data['buyer'];
        }

        if (isset($data['amount']) && filled($data['amount'])) {
            $transaction->amount = round($data['amount'], 2);
        }

        if ($transaction->isDirty()) {
            $transaction->save();
        }

        return $transaction;
    }
}
