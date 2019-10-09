<?php

namespace App\Billing;

use Illuminate\Support\Collection;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Token;

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

    public function newChargesDuring(\Closure $callback): Collection
    {
        $lastCharge = $this->lastCharge();

        $callback($this);

        return $this->newChargesSince($lastCharge)->pluck('amount');
    }

    public function getValidTestToken(): string
    {
        return Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123'
            ]
        ], ['api_key' => $this->apiKey])->id;
    }

    private function newChargesSince($charge = null)
    {
        $charges = Charge::all(
            ['ending_before' => $charge ? $charge->id : null],
            ['api_key' => $this->apiKey])
        ['data'];

        return collect($charges);
    }

    private function lastCharge()
    {
        return Charge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey])
        ['data'][0];
    }
}
