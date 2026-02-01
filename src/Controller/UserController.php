<?php

namespace App\Controller;

use App\Repository\ToRegisterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour les fonctionnalités utilisateur
 */
class UserController extends AbstractController
{
    /**
     * Affiche les inscriptions de l'utilisateur connecté
     */
    #[Route('/my-registrations', name: 'default_my_registrations')]
    #[IsGranted('ROLE_USER')]
    public function myRegistrations(ToRegisterRepository $toRegisterRepository): Response
    {
        $user = $this->getUser();

        // Récupérer toutes les inscriptions de l'utilisateur
        $registrations = $toRegisterRepository->findBy(
            ['user' => $user],
            ['registeredAt' => 'DESC']
        );

        return $this->render('user/my_registrations.html.twig', [
            'registrations' => $registrations,
        ]);
    }
}
