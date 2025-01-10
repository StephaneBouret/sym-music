<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TicketController extends AbstractController
{
    #[Route('/billeterie', name: 'app_ticket')]
    public function index(TicketRepository $ticketRepository): Response
    {
        $passes = $ticketRepository->findTicketsByCategoryName('pass');
        $tickets = $ticketRepository->findTicketsByCategoryName('billet-jour');

        return $this->render('ticket/index.html.twig', [
            'passes' => $passes,
            'tickets' => $tickets
        ]);
    }

    #[Route('/billeterie/{id}', name: 'app_ticket_detail')]
    public function show(
        #[MapEntity(expr: 'repository.find(id)')]
        Ticket $ticket
    ) {
        dd($ticket);
    }
}
