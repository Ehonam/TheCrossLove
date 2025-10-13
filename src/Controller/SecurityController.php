<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Connexion d'un utilisateur
     */
    #[Route('/login', name: 'security_connexion', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupération du message d'erreur s'il y en a un
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier email saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affichage du Formulaire
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'security_deconnexion')]
    public function logout(): void
    {
        // Cette méthode peut rester vide - elle sera interceptée par la clé logout de votre firewall
    }
}
