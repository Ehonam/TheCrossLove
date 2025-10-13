<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    /**
     * Inscription d'un utilisateur
     */
    #[Route('/inscription', name: 'security_inscription', methods: ['GET', 'POST'])]
    public function inscription(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        $error = null;

        // Création d'un nouvel utilisateur
        $user = new User();
        $user->setRole('Utilisateur');

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $plainPassword = $request->request->get('password');
            $confirmPassword = $request->request->get('confirmPassword');

            // Validation basique
            if ($plainPassword !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($plainPassword) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } else {
                $user->setEmail($email);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);

                // Hashage du mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                try {
                    // Insertion en BDD
                    $entityManager->persist($user);
                    $entityManager->flush();

                    // Message de succès
                    $this->addFlash('success', 'Félicitation, votre inscription est effective. Vous pouvez maintenant vous connecter.');

                    // Redirection vers la page de connexion
                    return $this->redirectToRoute('security_connexion');
                } catch (\Exception $e) {
                    $error = 'Cet email est déjà utilisé.';
                }
            }
        }

        // Affichage du Formulaire
        return $this->render('security/inscription.html.twig', [
            'error' => $error,
        ]);
    }
}
