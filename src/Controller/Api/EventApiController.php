<?php

namespace App\Controller\Api;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class EventApiController extends AbstractController
{
    #[Route('/events/summary', name: 'api_events_summary', methods: ['GET'])]
    public function summary(EventRepository $eventRepository): JsonResponse
    {
        $now = new \DateTime();

        // Recuperer tous les evenements actifs
        $allEvents = $eventRepository->findBy(['status' => 'active'], ['dateStart' => 'ASC']);

        $pastEvents = [];
        $ongoingEvents = [];
        $upcomingEvents = [];

        foreach ($allEvents as $event) {
            $eventData = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'description' => mb_substr($event->getDescription(), 0, 150) . '...',
                'dateStart' => $event->getDateStart()->format('d/m/Y H:i'),
                'dateEnd' => $event->getDateEnd()->format('d/m/Y H:i'),
                'city' => $event->getCity(),
                'country' => $event->getCountry(),
                'address' => $event->getFullAddress(),
                'organizer' => $event->getOrganizer(),
                'category' => $event->getCategory()?->getName(),
                'participantCount' => $event->getParticipantCount(),
                'maxParticipants' => $event->getMaxParticipants(),
                'availableSeats' => $event->getAvailableSeats(),
            ];

            if ($event->isPast()) {
                $pastEvents[] = $eventData;
            } elseif ($event->isOngoing()) {
                $ongoingEvents[] = $eventData;
            } else {
                $upcomingEvents[] = $eventData;
            }
        }

        return $this->json([
            'success' => true,
            'data' => [
                'past' => [
                    'count' => count($pastEvents),
                    'events' => array_slice($pastEvents, 0, 5),
                ],
                'ongoing' => [
                    'count' => count($ongoingEvents),
                    'events' => $ongoingEvents,
                ],
                'upcoming' => [
                    'count' => count($upcomingEvents),
                    'events' => array_slice($upcomingEvents, 0, 5),
                ],
            ],
            'faq' => [
                [
                    'question' => 'Quels sont les evenements a venir ?',
                    'key' => 'upcoming',
                ],
                [
                    'question' => 'Y a-t-il des evenements en cours ?',
                    'key' => 'ongoing',
                ],
                [
                    'question' => 'Quels evenements sont passes ?',
                    'key' => 'past',
                ],
                [
                    'question' => 'Comment m\'inscrire a un evenement ?',
                    'key' => 'register',
                ],
                [
                    'question' => 'Ou se trouve M2I Schiltigheim ?',
                    'key' => 'm2i_location',
                ],
            ],
        ]);
    }

    #[Route('/events/{id}', name: 'api_event_show', methods: ['GET'])]
    public function show(int $id, EventRepository $eventRepository): JsonResponse
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            return $this->json([
                'success' => false,
                'message' => 'Evenement non trouve',
            ], 404);
        }

        return $this->json([
            'success' => true,
            'data' => [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'dateStart' => $event->getDateStart()->format('d/m/Y H:i'),
                'dateEnd' => $event->getDateEnd()->format('d/m/Y H:i'),
                'city' => $event->getCity(),
                'country' => $event->getCountry(),
                'address' => $event->getFullAddress(),
                'organizer' => $event->getOrganizer(),
                'category' => $event->getCategory()?->getName(),
                'participantCount' => $event->getParticipantCount(),
                'maxParticipants' => $event->getMaxParticipants(),
                'availableSeats' => $event->getAvailableSeats(),
                'status' => $event->getComputedStatus(),
                'statusLabel' => $event->getStatusLabel(),
            ],
        ]);
    }

    /**
     * Récupère les positions GPS des participants d'un événement (Admin uniquement)
     */
    #[Route('/events/{id}/participants/locations', name: 'api_event_participants_locations', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function participantsLocations(Event $event): JsonResponse
    {
        $participants = [];

        foreach ($event->getRegistrations() as $registration) {
            if ($registration->hasLocation()) {
                $participants[] = [
                    'id' => $registration->getId(),
                    'name' => $registration->getUser()->getFullName(),
                    'whatsapp' => $registration->getWhatsappNumber(),
                    'whatsappLink' => $registration->getWhatsappLocationLink(),
                    'lat' => (float) $registration->getLatitude(),
                    'lng' => (float) $registration->getLongitude(),
                    'updatedAt' => $registration->getLocationUpdatedAt()?->format('d/m/Y H:i'),
                    'status' => $registration->getStatus(),
                ];
            }
        }

        return $this->json([
            'success' => true,
            'event' => [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'address' => $event->getFullAddress(),
                'lat' => $event->getLatitude(),
                'lng' => $event->getLongitude(),
            ],
            'participants' => $participants,
            'totalParticipants' => $event->getParticipantCount(),
            'participantsWithLocation' => count($participants),
        ]);
    }
}
