<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{
    public function __construct(protected CartService $cartService, protected EntityManagerInterface $em, protected PurchasePersister $persister)
    {
    }

    #[Route('/purchase/confirm', name: 'app_purchase_confirm')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function confirm(Request $request)
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('CAN_EDIT', $user, 'Vous n\'avez pas confimé votre email');

        // 1. Nous voulons lire les données du formulaire
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);
        // 2. Si le formulaire n'est pas soumis : redirection
        if (!$form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('app_cart_show');
        }

        // 4. S'il n'y a pas de billets dans le panier : redirection
        $cartItems = $this->cartService->getDetailedCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return $this->redirectToRoute('app_cart_show');
        }

        // 5. Nous allons créer une Purchase
        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);

        // 8. Nous allons enregistrer la commande
        $this->em->flush();
        // $this->cartService->empty();
        // $this->addFlash('success', 'La commande a bien été enregistrée');
        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }
}
