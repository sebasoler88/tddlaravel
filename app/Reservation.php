<?php


namespace App;


class Reservation
{

    private $tickets;
    private $email;

    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function complete($paymentGateway, $paymentToken)
    {
        $paymentGateway->charge($this->totalCost(), $paymentToken);

        return Order::forTickets(
            $this->tickets(),
            $this->email(),
            $this->totalCost()
        );
    }

    public function cancel()
    {
        $this->tickets->each(function (Ticket $ticket) {
            $ticket->release();
        });
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }
}
