<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProgramController extends AbstractController
{
    public function __construct(protected ArtistRepository $artistRepository)
    {}

    #[Route('/programmation', name: 'app_program')]
    public function index(): Response
    {
        $artists = $this->artistRepository->findAll();

        return $this->render('program/index.html.twig', [
            'artists' => $artists,
        ]);
    }

    #[Route('/programmation/{slug}', name: 'app_program_show', priority: -1)]
    public function show($slug): Response
    {
        $artist = $this->artistRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$artist) {
            throw $this->createNotFoundException("L'artiste demandé n'existe pas !");
        }

        return $this->render('program/show.html.twig', [
            'artist' => $artist
        ]);
    }
}
