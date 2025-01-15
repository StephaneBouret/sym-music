<?php

namespace App\Cart;

use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(protected RequestStack $requestStack, protected TicketRepository $ticketRepository)
    {}

    public function add(int $id)
    {
        $session = $this->requestStack->getSession();
        // 1. Retrouver le panier dans la session (sous la forme d'un tableau)
        // 2. S'il n'existe pas encore, alors on prend un tableau vide
        $cart = $session->get('cart', []);
        // 3. Voir si le billet {id} existe déjà dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        // 5. Sinon, ajouter le billet avec la quantité 1
        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        // 6. Enregistrer le tableau mis à jour dans la session
        $session->set('cart', $cart);
    }

    public function remove(int $id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        unset($cart[$id]);
        $session->set('cart', $cart);
    }

    public function decrement(int $id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        if (!array_key_exists($id, $cart)) {
            return;
        }
        // Soit le billet = 1, alors il faut le supprimer
        if ($cart[$id] === 1) {
            $this->remove($id);
        } else {
            // Soit le billet est à plus de 1, alors il faut décrémenter
            $cart[$id]--;
            $session->set('cart', $cart);
        }
        
    }

    public function getTotal(): int
    {
        $session = $this->requestStack->getSession();

        $total = 0;

        foreach ($session->get('cart', []) as $id => $qty) {
            $ticket = $this->ticketRepository->find($id);

            if (!$ticket) {
                continue;
            }

            $total += $ticket->getPrice() * $qty;
        }

        return $total;
    }

    public function getDetailedCartItems(): array
    {
        $session = $this->requestStack->getSession();
        
        $detailedCart = [];

        foreach ($session->get('cart', []) as $id => $qty) {
            $ticket = $this->ticketRepository->find($id);

            if (!$ticket) {
                continue;
            }

            $detailedCart[] = new CartItem($ticket, $qty);

            // $detailedCart[] = [
            //     'ticket' => $ticket,
            //     'qty' => $qty,
            //     'days' => $daysArray
            // ];
        }

        return $detailedCart;
    }
}