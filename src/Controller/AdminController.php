<?php


namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'events' => $events,
            'stats' => [
                'totalEvents' => count($events),
                'activeEvents' => 0, // Implement your logic
                'totalRegistrations' => 2719, // Implement your logic
                'fillRate' => 65, // Implement your logic
            ],
        ]);
    }
}
