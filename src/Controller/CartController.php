<?php

namespace App\Controller;

use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'app_cart', requirements: ['id' => '\d+'])]
    public function add($id, Request $request, TicketRepository $ticketRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        // 0. Sécurisation : est-ce que le billet existe
        $ticket = $ticketRepository->find($id);
        if (!$ticket) {
            throw $this->createNotFoundException("Le billet $id n'existe pas");
        }
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

        $this->addFlash('success', "Le billet a bien été ajouté au panier");

        // $request->getSession()->remove('cart');
        // return $this->redirectToRoute('app_ticket_detail', [
        //     'id' => $ticket->getId()
        // ]);
        return $this->redirectToRoute('app_ticket');
    }

    #[Route('/cart', name: 'app_cart_show')]
    public function show(RequestStack $requestStack, TicketRepository $ticketRepository): Response
    {
        $session = $requestStack->getSession();
        
        $detailedCart = [];
        $total = 0;
        // [1 => ['ticket' => '...'], 'quantity' => qté]
        foreach ($session->get('cart', []) as $id => $qty) {
            $ticket = $ticketRepository->find($id);
            $content = $ticket->getContent();
            $days = strip_tags($content, '<br>');
            $daysArray = preg_split('/<br\s*\/?>/i', $days);
            $daysArray = array_map('trim', $daysArray);

            $detailedCart[] = [
                'ticket' => $ticket,
                'qty' => $qty,
                'days' => $daysArray
            ];

            $total += $ticket->getPrice() * $qty;
        }

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total
        ]);
    }
}
