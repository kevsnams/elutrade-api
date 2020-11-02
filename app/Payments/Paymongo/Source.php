<?php
namespace App\Payments\Paymongo;

use Exception;

class Source
{
    protected string $type;

    protected string $authBasicToken;

    protected array $allowedTypes = [
        'gcash', 'grab_pay'
    ];

    public function __construct(string $type, string $authBasicToken)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new Exception('PAYMONGO: Source type `'. $type .'` is not allowed');
        }

        $this->type = $type;
    }
}
