<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use App\Reservation;

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

    public function store($concertId)
    {
        /** @var Concert $concert */
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email' => 'required|email',
            'ticket_quantity' => 'required|gte:1',
            'payment_token' => 'required'
        ]);

        try {
            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));
            $order = $reservation->complete($this->paymentGateway, request('payment_token'));

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            $reservation->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }

    }
}
