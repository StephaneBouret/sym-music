<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(protected TicketRepository $ticketRepository, protected CartService $cartService)
    {}

    #[Route('/cart/add/{id}', name: 'app_cart', requirements: ['id' => '\d+'])]
    public function add($id, Request $request): Response
    {
        // 0. Sécurisation : est-ce que le billet existe
        $ticket = $this->ticketRepository->find($id);
        if (!$ticket) {
            throw $this->createNotFoundException("Le billet $id n'existe pas");
        }

        $this->cartService->add($id);

        $this->addFlash('success', "Le billet a bien été ajouté au panier");

        // reroutage en cas d'incrémentation
        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute('app_cart_show');
        }

        return $this->redirectToRoute('app_ticket');
    }

    #[Route('/cart', name: 'app_cart_show')]
    public function show(): Response
    {
        $form = $this->createForm(CartConfirmationType::class);
        $total = $this->cartService->getTotal();
        $detailedCart = $this->cartService->getDetailedCartItems();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'confirmationForm' => $form
        ]);
    }

    #[Route('/cart/delete/{id}', name: 'app_cart_delete', requirements: ['id' => '\d+'])]
    public function delete($id): Response
    {
        $ticket = $this->ticketRepository->find($id);
        if (!$ticket) {
            throw $this->createNotFoundException("Le billet $id n'existe pas et ne peut pas être supprimé !");
        }

        $this->cartService->remove($id);

        $this->addFlash('success', "Le billet a bien été supprimé du panier");

        return $this->redirectToRoute('app_cart_show');

    }

    #[Route('/cart/decrement/{id}', name: 'app_cart_decrement', requirements: ['id' => '\d+'])]
    public function decrement($id): Response
    {
        $ticket = $this->ticketRepository->find($id);
        if (!$ticket) {
            throw $this->createNotFoundException("Le billet $id n'existe pas et ne peut pas être supprimé !");
        }

        $this->cartService->decrement($id);

        $this->addFlash('success', "Le billet a bien été décrémenté");

        return $this->redirectToRoute('app_cart_show');
    }
}
