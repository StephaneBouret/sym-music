<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UpdateUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstname = ucfirst($form->get('firstname')->getData());
            $lastname = mb_strtoupper($form->get('lastname')->getData());

            $user->setFirstname($firstname)
                ->setLastname($lastname);
            $em->flush();

            $this->addFlash('success', 'Vos modifications ont bien été prises en compte');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form,
        ]);
    }
}
