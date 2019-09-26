<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $guarded = [];
    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
    }

    public function hasOrderFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    public function ordersFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->get();
    }

    public function orderTickets(string $email, int $ticketsQuantity)
    {
        $tickets = $this->findTickets($ticketsQuantity);

        return $this->createOrder($email, $tickets);
    }

    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $item) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    public function ticketsRemaining()
    {
        return $this->tickets()
            ->available()
            ->count();
    }

    public function reserveTickets($quantity, $email)
    {
        $tickets = $this->findTickets($quantity)
            ->each(function (Ticket $ticket) {
                $ticket->reserve();
            });

        return new Reservation($tickets, $email);
    }

    public function findTickets(int $quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();

        if ($tickets->count() < $quantity) {
            throw new NotEnoughTicketsException;
        }
        return $tickets;
    }

    public function createOrder(string $email, $tickets): Model
    {
        return Order::forTickets($tickets, $email, $tickets->sum('price'));
    }

}
