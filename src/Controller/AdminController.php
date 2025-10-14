<?php


namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        // Calcul des statistiques réelles
        $activeEvents = 0;
        $totalRegistrations = 0;

        foreach ($events as $event) {
            if ($event->isUpcoming()) {
                $activeEvents++;
            }
            $totalRegistrations += $event->getParticipantCount();
        }

        // Calcul du taux de remplissage moyen
        $totalSeats = 0;
        $eventsWithLimit = 0;
        foreach ($events as $event) {
            if ($event->getMaxParticipants() !== null) {
                $totalSeats += $event->getMaxParticipants();
                $eventsWithLimit++;
            }
        }
        $fillRate = $eventsWithLimit > 0 ? round(($totalRegistrations / $totalSeats) * 100) : 0;

        return $this->render('admin/dashboard.html.twig', [
            'events' => $events,
            'stats' => [
                'totalEvents' => count($events),
                'activeEvents' => $activeEvents,
                'totalRegistrations' => $totalRegistrations,
                'fillRate' => $fillRate,
            ],
        ]);
    }

    #[Route('/events', name: 'admin_events')]
    public function events(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/events.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/new', name: 'admin_event_new', methods: ['GET', 'POST'])]
    public function newEvent(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur connecté comme créateur
            $event->setCreatedBy($this->getUser());

            // Générer le slug
            $event->computeSlug($slugger);

            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/events',
                        $newFilename
                    );
                    $event->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès !');

            return $this->redirectToRoute('admin_events');
        }

        return $this->render('admin/event_form.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'title' => 'Créer un événement'
        ]);
    }

    #[Route('/event/{id}/edit', name: 'admin_event_edit', methods: ['GET', 'POST'])]
    public function editEvent(
        Event $event,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Régénérer le slug si le titre a changé
            $event->computeSlug($slugger);

            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Supprimer l'ancienne image si elle existe
                if ($event->getImage()) {
                    $oldImagePath = $this->getParameter('kernel.project_dir').'/public/uploads/events/'.$event->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/events',
                        $newFilename
                    );
                    $event->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès !');

            return $this->redirectToRoute('admin_events');
        }

        return $this->render('admin/event_form.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'title' => 'Modifier l\'événement'
        ]);
    }

    #[Route('/event/{id}/delete', name: 'admin_event_delete', methods: ['POST'])]
    public function deleteEvent(
        Event $event,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été supprimé avec succès !');
        } else {
            $this->addFlash('error', 'Token de sécurité invalide.');
        }

        return $this->redirectToRoute('admin_events');
    }

    #[Route('/event/{id}/participants', name: 'admin_event_participants')]
    public function eventParticipants(Event $event): Response
    {
        return $this->render('admin/event_participants.html.twig', [
            'event' => $event,
            'registrations' => $event->getRegistrations(),
        ]);
    }
}
