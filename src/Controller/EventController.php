<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\ToRegister;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'default_event_index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $category = $request->query->get('category');
        $search = $request->query->get('search');
        $sortBy = $request->query->get('sort', 'date');

        if ($search) {
            $events = $eventRepository->search($search);
        } elseif ($category) {
            $events = $eventRepository->findByCategory($category);
        } else {
            $events = $eventRepository->findAllSorted($sortBy);
        }

        $categories = $eventRepository->findAllCategories();

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'categories' => $categories,
            'current_category' => $category,
            'current_search' => $search,
            'current_sort' => $sortBy,
        ]);
    }

    #[Route('/{id}', name: 'default_event_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez vous connecter pour vous inscrire à cet événement.');

            // Récupérer les événements similaires pour les afficher quand même
            $relatedEvents = $event->getCategory()
                ? $entityManager->getRepository(Event::class)->findBy(
                    ['category' => $event->getCategory()],
                    ['dateStart' => 'ASC'],
                    4
                )
                : [];

            $relatedEvents = array_filter($relatedEvents, fn($e) => $e->getId() !== $event->getId());
            $relatedEvents = array_slice($relatedEvents, 0, 3);

            return $this->render('event/show.html.twig', [
                'event' => $event,
                'registration_form' => null,
                'related_events' => $relatedEvents,
            ]);
        }

        // Créer l'inscription
        $toRegister = new ToRegister();
        $toRegister->setEvent($event);
        $toRegister->setUser($this->getUser());

        // Formulaire simple (juste un bouton submit)
        $form = $this->createFormBuilder($toRegister)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($event->getAvailableSeats() > 0) {
                $entityManager->persist($toRegister);
                $entityManager->flush();

                $this->addFlash('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé.');
                return $this->redirectToRoute('default_event_show', ['id' => $event->getId()]);
            } else {
                $this->addFlash('error', 'Désolé, il n\'y a plus de places disponibles pour cet événement.');
            }
        }

        // Récupérer les événements similaires
        $relatedEvents = $event->getCategory()
            ? $entityManager->getRepository(Event::class)->findBy(
                ['category' => $event->getCategory()],
                ['dateStart' => 'ASC'],
                4
            )
            : [];

        $relatedEvents = array_filter($relatedEvents, fn($e) => $e->getId() !== $event->getId());
        $relatedEvents = array_slice($relatedEvents, 0, 3);

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'registration_form' => $form->createView(),
            'related_events' => $relatedEvents,
        ]);
    }

    #[Route('/category/{category}', name: 'default_event_category', methods: ['GET'])]
    public function category(string $category, EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findByCategory($category);
        $categories = $eventRepository->findAllCategories();

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'categories' => $categories,
            'current_category' => $category,
        ]);
    }
}
