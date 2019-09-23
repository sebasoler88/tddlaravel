<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;

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
            'ticket_quantity' => 'required|gt:1',
            'payment_token' => 'required'
        ]);

        try {
            /** @var Order $order */
            $order = $concert->orderTickets(request('email'), request('ticket_quantity'));
            $this->paymentGateway->charge(
                $concert->ticket_price * request('ticket_quantity'),
                request('payment_token')
            );

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            $order->cancel();

            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }

    }
}
