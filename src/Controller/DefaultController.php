<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    /**
     * Page d'accueil
     */
    #[Route('/', name: 'default_home')]
    public function index(EventRepository $eventRepository, CategoryRepository $categoryRepository): Response
    {
        // Récupérer uniquement les événements actifs
        $events = $eventRepository->findBy(['status' => 'active']);

        // Récupérer les catégories des événements publiés (actifs)
        $categoriesWithEvents = [];
        foreach ($events as $event) {
            $category = $event->getCategory();
            if ($category && !isset($categoriesWithEvents[$category->getId()])) {
                $categoriesWithEvents[$category->getId()] = $category;
            }
        }

        return $this->render('default/home.html.twig', [
            'events' => $events,
            'categories' => array_values($categoriesWithEvents),
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

}
