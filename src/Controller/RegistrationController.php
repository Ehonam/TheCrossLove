<?php
namespace App\Controller;

use App\Entity\Event;
use App\Entity\ToRegister;
use App\Repository\ToRegisterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur gérant les inscriptions aux événements
 *
 * 🎓 EXPLICATION :
 * - #[Route('/registration')] : Préfixe pour toutes les routes de ce contrôleur
 * - #[IsGranted('ROLE_USER')] : Toutes les méthodes nécessitent d'être connecté
 */
#[Route('/registration')]
#[IsGranted('ROLE_USER')]
class RegistrationController extends AbstractController
{
    /**
     * Inscription à un événement
     *
     * EXPLICATION :
     * - Route : /registration/event/{id}/register
     * - Méthode : POST uniquement (pour la sécurité)
     * - Symfony injecte automatiquement l'Event via le ParamConverter
     * - EntityManagerInterface permet de sauvegarder en base
     * - ToRegisterRepository permet de vérifier les inscriptions existantes
     */
    #[Route('/event/{id}/register', name: 'app_event_register', methods: ['POST'])]
    public function register(
        Event $event,
        EntityManagerInterface $entityManager,
        ToRegisterRepository $registrationRepository
    ): Response
    {
        // 📖 ÉTAPE 1 : Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // 📖 ÉTAPE 2 : Vérifier si l'événement est encore à venir
        if (!$event->isUpcoming()) {
            $this->addFlash('error', 'Cet événement est déjà passé ou a déjà commencé.');
            return $this->redirectToRoute('default_event_show', ['id' => $event->getId()]);
        }

        // 📖 ÉTAPE 3 : Vérifier si l'utilisateur n'est pas déjà inscrit
        if ($event->isUserRegistered($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
            return $this->redirectToRoute('default_event_show', ['id' => $event->getId()]);
        }

        // 📖 ÉTAPE 4 : Vérifier les places disponibles
        $availableSeats = $event->getAvailableSeats();

        // Si null = places illimitées, on peut s'inscrire
        // Si > 0 = il reste des places
        // Si <= 0 = plus de place
        if ($availableSeats !== null && $availableSeats <= 0) {
            $this->addFlash('error', 'Désolé, il n\'y a plus de places disponibles pour cet événement.');
            return $this->redirectToRoute('default_event_show', ['id' => $event->getId()]);
        }

        // 📖 ÉTAPE 5 : Créer l'inscription
        $registration = new ToRegister();
        $registration->setUser($user);
        $registration->setEvent($event);
        $registration->setStatus('confirmed'); // Déjà fait dans le constructeur, mais on peut le forcer

        // 📖 ÉTAPE 6 : Persister en base de données
        try {
            $entityManager->persist($registration);
            $entityManager->flush();

            // 📖 ÉTAPE 7 : Message de succès et redirection
            $this->addFlash('success', 'Votre inscription a été confirmée ! Vous recevrez un email de
  confirmation.');

        } catch (\Exception $e) {
            // 📖 ÉTAPE 8 : Gestion des erreurs (ex: doublon)
            $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
        }

        return $this->redirectToRoute('default_event_show', ['id' => $event->getId()]);
    }

    /**
     * Désinscription d'un événement
     *
     * 🎓 EXPLICATION :
     * - Route : /registration/event/{id}/unregister
     * - Méthode : POST uniquement (pour la sécurité)
     * - On récupère l'inscription existante et on la supprime
     */
    #[Route('/event/{id}/unregister', name: 'app_event_unregister', methods: ['POST'])]
    public function unregister(
        Event $event,
        EntityManagerInterface $entityManager,
        ToRegisterRepository $registrationRepository
    ): Response
    {
        $user = $this->getUser();

        //  ÉTAPE 1 : Trouver l'inscription existante
        $registration = $registrationRepository->findOneBy([
            'user' => $user,
            'event' => $event
        ]);

        //  ÉTAPE 2 : Vérifier que l'inscription existe
        if (!$registration) {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cet événement.');
            return $this->redirectToRoute('default_event_show', ['id' => $event->getId()]);
        }

        //  ÉTAPE 3 : Vérifier si l'annulation est possible
        if (!$registration->canBeCancelled()) {
            $this->addFlash('error', 'Vous ne pouvez plus annuler votre inscription à cet événement.');
            return $this->redirectToRoute('default_event_show', ['id' => $event->getId()]);
        }

        //  ÉTAPE 4 : Supprimer l'inscription
        try {
            $entityManager->remove($registration);
            $entityManager->flush();

            $this->addFlash('success', 'Votre inscription a été annulée avec succès.');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'annulation.');
        }

        return $this->redirectToRoute('default_event_show', ['id' => $event->getId()]);
    }
}
