<?php

namespace App\Http\Controllers;

use App\Http\Controllers\v1\TransactionController;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Vinkla\Hashids\Facades\Hashids;

class DevController extends Controller
{
    public function playground(Request $request)
    {
        dd(Hashids::encode(38));
    }
}
