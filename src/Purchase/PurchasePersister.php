<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PurchasePersister
{
    public function __construct(protected Security $security, protected CartService $cartService, protected EntityManagerInterface $em)
    {}

    public function storePurchase(Purchase $purchase)
    {
        // 6. Nous allons la lier à l'utilisateur actuellement connecté
        $purchase->setUser($this->security->getUser())
            ->setTotal($this->cartService->getTotal());
        $this->em->persist($purchase);
        // 7. Nous allons la lier avec les billets qui sont dans le panier
        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setTicket($cartItem->ticket)
                ->setTicketName($cartItem->ticket->getFullName())
                ->setTicketPrice($cartItem->ticket->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());

            $this->em->persist($purchaseItem);
        }
    }
}