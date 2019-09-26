<?php

namespace App\Billing;

use Stripe\Charge;
use Stripe\Exception\ApiErrorException;

class StripePaymentGateway implements PaymentGateway
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge(int $amount, string $token)
    {
        try {
            Charge::create([
                'amount' => $amount,
                'currency' => "usd",
                'source' => $token,
                'description' => 'Charge for jenny.rosen@example.com'
            ], ['api_key' => $this->apiKey]);
        } catch (ApiErrorException $e) {
            throw new PaymentFailedException($e);
        }
    }
}
