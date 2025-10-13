<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends AbstractController
{
    /**
     * Page d'accueil
     */
    #[Route('/', name: 'default_home')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('default/home.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * Liste des événements
     */
    #[Route('/events', name: 'default_event_index')]
    public function eventIndex(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * Créer un nouvel événement (uniquement pour les admins)
     */
    #[Route('/event/new', name: 'default_event_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        $event = new Event();
        // Set default values or form handling would go here (e.g., using a FormType)
        $event->setCreatedAt(new \DateTimeImmutable());

        if ($request->isMethod('POST')) {
            // Simplified example; use a form for real-world scenarios
            $event->setTitle($request->request->get('title'));
            $event->setDescription($request->request->get('description'));
            $event->setAddress($request->request->get('address'));
            $event->setPostalCode($request->request->get('postal_code'));
            $event->setCity($request->request->get('city'));
            $event->setCountry($request->request->get('country'));
            $event->setDateStart(new \DateTime($request->request->get('date_start')));
            $event->setDateEnd(new \DateTime($request->request->get('date_end')));
            $event->setCreatedBy($this->getUser());

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès.');
            return $this->redirectToRoute('default_event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * Connexion d'un utilisateur
     */
    #[Route('/connexion', name: 'default_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupération du message d'erreur s'il y en a un
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier email saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affichage du Formulaire
        return $this->render('to_register/connexion.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Déconnexion
     */
    #[Route('/deconnexion', name: 'default_logout')]
    public function logout(): void
    {
        // Cette méthode peut rester vide, gérée par le firewall de sécurité
    }

    /**
     * Inscription d'un utilisateur
     */
    #[Route('/inscription', name: 'default_register', methods: ['GET', 'POST'])]
    public function register(
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
                    $this->addFlash(
                        'success',
                        'Félicitation, votre inscription est effective. Vous pouvez maintenant vous connecter.'
                    );

                    // Redirection vers la page de connexion
                    return $this->redirectToRoute('default_login');
                } catch (\Exception $e) {
                    $error = 'Cet email est déjà utilisé.';
                }
            }
        }

        // Affichage du Formulaire
        return $this->render('to_register/to_register.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * Mes inscriptions (page utilisateur)
     */
    #[Route('/my-registrations', name: 'default_my_registrations')]
    public function myRegistrations(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('default_login');
        }

        $user = $this->getUser();
        // Assuming ToRegister entity has a relation to User
        $registrations = $user->getRegistrations(); // Adjust based on your User entity

        return $this->render('user/my_registrations.html.twig', [
            'registrations' => $registrations,
        ]);
    }

    /**
     * Dashboard Admin
     */
    #[Route('/admin/dashboard', name: 'default_admin_dashboard')]
    public function adminDashboard(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * Gestion des événements (Admin)
     */
    #[Route('/admin/events', name: 'default_admin_events')]
    public function adminEvents(EventRepository $eventRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        $events = $eventRepository->findAll();

        return $this->render('admin/events.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * Gestion des catégories (Admin)
     */
    #[Route('/admin/categories', name: 'default_admin_categories')]
    public function adminCategories(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        // Add category repository or service if needed
        return $this->render('admin/categories.html.twig');
    }
}
