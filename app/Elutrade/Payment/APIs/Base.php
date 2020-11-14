<?php
namespace App\Elutrade\Payment\APIs;

use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class Base
{
    public int $mode;

    protected $app;
    protected $logger;
    protected bool $shouldLog;
    protected Transaction $transaction;
    private $response = [];

    public function __construct($app, Transaction $transaction, int $mode)
    {
        $this->app = $app;
        $this->mode = $mode;
        $this->transaction = $transaction;
        $this->logger = TransactionLog::class;
        $this->shouldLog = $this->app->config('elutrade.payments.log');
    }

    public function log(string $message, array $details = []) : void
    {
        if ($this->shouldLog) {
            $this->logger::create([
                'transaction_id' => $this->transaction->id,
                'description' => $message,
                'json_details' => json_encode($details)
            ]);
        }
    }

    public function setResponse($response) : void
    {
        $this->response = $response;
    }

    public function getResponse() : array
    {
        if (is_object($this->response)) {
            return (array) $this->response;
        }

        return $this->response;
    }

    public function toResponseJson() : JsonResponse
    {
        return Response::json($this->getResponse());
    }
}
