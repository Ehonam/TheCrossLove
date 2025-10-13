<?php


namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'calendar_index')]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('calendar/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }
}
