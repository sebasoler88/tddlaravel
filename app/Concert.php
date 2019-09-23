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
        return $this->hasMany(Order::class);
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
        $tickets = $this->tickets()
            ->available()
            ->take($ticketsQuantity)
            ->get();

        if ($tickets->count() < $ticketsQuantity) {
            throw new NotEnoughTicketsException;
        }

        $order = $this->orders()->create([
            'email' => $email,
            'amount' => $ticketsQuantity * $this->ticket_price,
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
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

}
