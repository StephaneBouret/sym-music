<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em, protected CartService $cartService)
    {}

    #[Route('/purchase/terminate/{id}', name: 'app_payment_success')]
    #[IsGranted('ROLE_USER')]
    public function success($id, PurchaseRepository $purchaseRepository, EventDispatcherInterface $dispatcher): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('CAN_EDIT', $user, 'Vous n\'avez pas confimé votre email');
        // 1. Récupérer la commande
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase || 
            ($purchase && $purchase->getUser() !== $user) || 
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash("warning", "La commande n'existe pas !");
            return $this->redirectToRoute('app_purchase_list');
        }
        // 2. La faire passer au status PAID
        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->em->flush();
        // 3. Je veux vider le panier
        $this->cartService->empty();

        // 3.1 Lancer un événement qui permet de réagir à la prise d'une commande (ex. envoi de mail)
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');
        
        // 4. Redirection avec un addflash vers la liste des commandes
        $this->addFlash("success", "La commande a été payée et confirmée !");
        return $this->redirectToRoute('app_purchase_list');
    }
}