<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;

class ConcertOrdersController extends Controller
{
    /**
     * @var PaymentGateway
     */
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store(Concert $concert)
    {
        $this->validate(request(), [
            'email' => 'required|email',
            'ticket_quantity' => 'required|gt:1',
            'payment_token' => 'required'
        ]);

        $this->paymentGateway->charge($concert->ticket_price * request('ticket_quantity'), request('payment_token'));

        $concert->orderTickets(request('email'), request('ticket_quantity'));

        return response()->json([], 201);
    }
}
