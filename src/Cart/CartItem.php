<?php

namespace App\Cart;

use App\Entity\Ticket;

class CartItem
{
    public $ticket;
    public $qty;
    protected $days;

    public function __construct(Ticket $ticket, int $qty)
    {
        $this->ticket = $ticket;
        $this->qty = $qty;
        $this->days = $this->extractDays($ticket->getContent());
    }

    public function getTotal(): int
    {
        return $this->ticket->getPrice() * $this->qty;
    }

    public function getDays(): array
    {
        return $this->days;
    }

    private function extractDays(string $content): array
    {
        $days = strip_tags($content, '<br>');
        $daysArray = preg_split('/<br\s*\/?>/i', $days);
        return array_map('trim', $daysArray);
    }
}