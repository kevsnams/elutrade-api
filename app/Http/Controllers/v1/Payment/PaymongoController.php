<?php
namespace App\Http\Controllers\v1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Payments\Paymongo;
use Illuminate\Http\Request;

class PaymongoController extends Controller
{
    /*
    TODO add to docs
    Example success response:
    {
        "data": {
            "id": "src_Mgt1Am44bA8PAwYMPQeMdZgs",
            "type": "source",
            "attributes": {
                "amount": 10000,
                "billing": null,
                "currency": "PHP",
                "livemode": false,
                "redirect": {
                    "checkout_url": "https://test-sources.paymongo.com/sources?id=src_Mgt1Am44bA8PAwYMPQeMdZgs",
                    "failed": "http://test.com",
                    "success": "http://test.com"
                },
                "status": "pending",
                "type": "gcash",
                "created_at": 1604693446,
                "updated_at": 1604693446
            }
        }
    }
    */

    /*
    Example error response:
    {
        "errors": [
            {
                "code": "parameter_required",
                "detail": "currency is required.",
                "source": {
                    "pointer": "currency",
                    "attribute": "currency"
                }
            },
            {
                "code": "parameter_required",
                "detail": "source_type is required.",
                "source": {
                    "pointer": "source_type",
                    "attribute": "source_type"
                }
            }
        ]
    }
    */
    public function gcash(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ]
        ]);

        $transaction = Transaction::ofBuyer($request->user()->id)->findByHashidOrFail($request->transaction);
        $pmResponse = Paymongo::gcash($transaction);

        if ($pmResponse->failed()) {
            if ($pmResponse->status() === 401) {
                return response()
                    ->setStatusCode($pmResponse->status())
                    ->json(['message' => 'Unauthorized']);
            }

            return $pmResponse->throw()->json();
        }

        if ($pmResponse->successful()) {
            $transaction->payment()->save(
                new TransactionPayment([
                    'mode' => TransactionPayment::MODE_PAYMONGO_GCASH,
                    'paymongo_source_id' => $pmResponse['data']['id']
                ])
            );

            $transaction->refresh();
        }

        return [
            'success' => true,
            'transaction' => $transaction,
            'paymongo' => $pmResponse->json()
        ];
    }

    public function grabPay(Request $request)
    {
        $request->validate([
            'transaction' => [
                'required', 'string'
            ]
        ]);

        $transaction = Transaction::ofBuyer($request->user()->id)->findByHashidOrFail($request->transaction);
        $pmResponse = Paymongo::grabPay($transaction);

        if ($pmResponse->failed()) {
            if ($pmResponse->status() === 401) {
                return response()
                    ->setStatusCode($pmResponse->status())
                    ->json(['message' => 'Unauthorized']);
            }

            return $pmResponse->throw()->json();
        }

        if ($pmResponse->successful()) {
            $transaction->payment()->save(
                new TransactionPayment([
                    'mode' => TransactionPayment::MODE_PAYMONGO_GRABPAY,
                    'paymongo_source_id' => $pmResponse['data']['id']
                ])
            );

            $transaction->refresh();
        }

        return [
            'success' => true,
            'transaction' => $transaction,
            'paymongo' => $pmResponse->json()
        ];
    }
}
