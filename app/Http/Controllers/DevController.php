<?php

namespace App\Http\Controllers;

use App\Http\Controllers\v1\TransactionController;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class DevController extends Controller
{
    public function playground(Request $request)
    {
        // $user = User::find(1);
        // // w1WGeXmZVjQqvomWxM7gapE024rOdR
        // dd($user->hashid());
        return new ApiResource(null);
    }
}
