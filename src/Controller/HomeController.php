<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(protected MessageRepository $messageRepository, protected ArtistRepository $artistRepository)
    {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $artists = $this->artistRepository->findNextTwoProgramDates();
        $messages = $this->messageRepository->findAll();

        return $this->render('home/index.html.twig', [
            'artists' => $artists,
            'messages' => $messages
        ]);
    }
}
