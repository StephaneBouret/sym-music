<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseListController extends AbstractController
{
    #[Route('/purchases', name: 'app_purchase_list')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à vos commandes')]
    public function index(): Response
    {
        // 1. Nous devons nous assurer que la personne est connectée sinon redirection vers la page de connexion
        // 2. Nous voulons savoir qui est connecté
        /** @var User */
        $user = $this->getUser();

        // 3. Nous voulons passer l'utilisateur à TWIG afin d'afficher ses commandes
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);
    }
}
