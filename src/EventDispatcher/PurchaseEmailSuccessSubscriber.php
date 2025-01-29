<?php

namespace App\EventDispatcher;

use App\Entity\User;
use App\Service\SendMailService;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseEmailSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(protected SendMailService $sendMailService, protected Security $security)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }

    public function SendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        // 1. Récupérer l'utilisateur connecté
        /** @var User $user */
        $user = $this->security->getUser();

        // 2. Je veux récupérer la commande
        $purchase = $purchaseSuccessEvent->getPurchase();

        // 3. J'envoie le mail
        $this->sendMailService->sendMail(
            null,
            'Votre commande',
            $user->getEmail(),
            "Bravo votre commande n°{$purchase->getId()} a bien été confirmée",
            'purchase_success',
            [
                'purchase' => $purchase,
                'user' => $user
            ]
        );
    }    
}