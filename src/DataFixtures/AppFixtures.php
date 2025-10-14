<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\ToRegister;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private SluggerInterface $slugger;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // Création des utilisateurs
        $users = $this->createUsers($manager);

        // Création des catégories
        $categories = $this->createCategories($manager);

        // Création des événements
        $events = $this->createEvents($manager, $users, $categories);

        // Création des inscriptions
        $this->createRegistrations($manager, $users, $events);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager): array
    {
        $users = [];

        // Utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@eventhub.fr');
        $admin->setFirstName('Admin');
        $admin->setLastName('Système');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);
        $users['admin'] = $admin;

        // Utilisateurs standards
        $standardUsers = [
            ['email' => 'john.doe@example.com', 'firstName' => 'John', 'lastName' => 'Doe'],
            ['email' => 'marie.martin@example.com', 'firstName' => 'Marie', 'lastName' => 'Martin'],
            ['email' => 'pierre.bernard@example.com', 'firstName' => 'Pierre', 'lastName' => 'Bernard'],
            ['email' => 'sophie.dubois@example.com', 'firstName' => 'Sophie', 'lastName' => 'Dubois'],
            ['email' => 'lucas.petit@example.com', 'firstName' => 'Lucas', 'lastName' => 'Petit'],
            ['email' => 'emma.robert@example.com', 'firstName' => 'Emma', 'lastName' => 'Robert'],
            ['email' => 'thomas.richard@example.com', 'firstName' => 'Thomas', 'lastName' => 'Richard'],
            ['email' => 'julie.moreau@example.com', 'firstName' => 'Julie', 'lastName' => 'Moreau'],
        ];

        foreach ($standardUsers as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    private function createCategories(ObjectManager $manager): array
    {
        $categoriesData = [
            'Conférences',
            'Ateliers',
            'Networking',
            'Meetups',
            'Webinaires',
            'Hackathons',
        ];

        $categories = [];
        foreach ($categoriesData as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->computeSlug($this->slugger);
            $manager->persist($category);
            $categories[] = $category;
        }

        return $categories;
    }

    private function createEvents(ObjectManager $manager, array $users, array $categories): array
    {
        $eventsData = [
            [
                'title' => 'Symfony 7 : Les nouveautés',
                'description' => 'Découvrez toutes les nouveautés de Symfony 7 et comment migrer vos applications existantes.',
                'dateStart' => new \DateTime('+7 days 14:00'),
                'dateEnd' => new \DateTime('+7 days 18:00'),
                'address' => '10 Rue de la Paix',
                'postalCode' => '75002',
                'city' => 'Paris',
                'country' => 'France',
                'organizer' => 'SensioLabs',
                'maxParticipants' => 50,
                'categoryIndex' => 0,
            ],
            [
                'title' => 'Atelier Docker pour débutants',
                'description' => 'Apprenez les bases de Docker et comment containeriser vos applications PHP.',
                'dateStart' => new \DateTime('+14 days 09:00'),
                'dateEnd' => new \DateTime('+14 days 17:00'),
                'address' => '25 Avenue des Champs-Élysées',
                'postalCode' => '75008',
                'city' => 'Paris',
                'country' => 'France',
                'organizer' => 'Docker France',
                'maxParticipants' => 30,
                'categoryIndex' => 1,
            ],
            [
                'title' => 'Meetup PHP Paris',
                'description' => 'Rencontre mensuelle de la communauté PHP parisienne. Présentations, discussions et networking.',
                'dateStart' => new \DateTime('+21 days 19:00'),
                'dateEnd' => new \DateTime('+21 days 22:00'),
                'address' => '42 Rue du Faubourg Saint-Antoine',
                'postalCode' => '75012',
                'city' => 'Paris',
                'country' => 'France',
                'organizer' => 'AFUP',
                'maxParticipants' => 80,
                'categoryIndex' => 3,
            ],
            [
                'title' => 'Webinaire : Sécurité des applications web',
                'description' => 'Webinaire sur les meilleures pratiques de sécurité pour vos applications web modernes.',
                'dateStart' => new \DateTime('+10 days 15:00'),
                'dateEnd' => new \DateTime('+10 days 16:30'),
                'address' => 'En ligne',
                'postalCode' => '00000',
                'city' => 'Internet',
                'country' => 'France',
                'organizer' => 'CyberSec France',
                'maxParticipants' => 200,
                'categoryIndex' => 4,
            ],
            [
                'title' => 'Hackathon Green Tech',
                'description' => 'Hackathon de 48h pour développer des solutions technologiques écologiques et durables.',
                'dateStart' => new \DateTime('+30 days 09:00'),
                'dateEnd' => new \DateTime('+32 days 18:00'),
                'address' => '15 Rue Jean Jaurès',
                'postalCode' => '69007',
                'city' => 'Lyon',
                'country' => 'France',
                'organizer' => 'GreenTech Lyon',
                'maxParticipants' => 100,
                'categoryIndex' => 5,
            ],
            [
                'title' => 'Conférence API Platform',
                'description' => 'Découvrez comment créer des API REST et GraphQL performantes avec API Platform.',
                'dateStart' => new \DateTime('+45 days 10:00'),
                'dateEnd' => new \DateTime('+45 days 18:00'),
                'address' => '8 Boulevard du Port',
                'postalCode' => '95000',
                'city' => 'Cergy',
                'country' => 'France',
                'organizer' => 'API Platform Team',
                'maxParticipants' => 60,
                'categoryIndex' => 0,
            ],
            [
                'title' => 'Atelier TDD avec PHPUnit',
                'description' => 'Atelier pratique sur le développement piloté par les tests avec PHPUnit et Symfony.',
                'dateStart' => new \DateTime('+60 days 09:30'),
                'dateEnd' => new \DateTime('+60 days 17:30'),
                'address' => '33 Rue de la République',
                'postalCode' => '13001',
                'city' => 'Marseille',
                'country' => 'France',
                'organizer' => 'Test Driven Marseille',
                'maxParticipants' => 25,
                'categoryIndex' => 1,
            ],
            [
                'title' => 'Networking Dev Full Stack',
                'description' => 'Soirée networking pour les développeurs full stack. Échanges, opportunités et convivialité.',
                'dateStart' => new \DateTime('+5 days 18:30'),
                'dateEnd' => new \DateTime('+5 days 22:00'),
                'address' => '50 Quai de la Loire',
                'postalCode' => '75019',
                'city' => 'Paris',
                'country' => 'France',
                'organizer' => 'DevConnect',
                'maxParticipants' => null,
                'categoryIndex' => 2,
            ],
            [
                'title' => 'Conférence Intelligence Artificielle & PHP',
                'description' => 'Comment intégrer des solutions d\'IA dans vos applications PHP modernes.',
                'dateStart' => new \DateTime('+75 days 13:00'),
                'dateEnd' => new \DateTime('+75 days 19:00'),
                'address' => '12 Avenue Malraux',
                'postalCode' => '67000',
                'city' => 'Strasbourg',
                'country' => 'France',
                'organizer' => 'AI & Dev France',
                'maxParticipants' => 120,
                'categoryIndex' => 0,
            ],
            [
                'title' => 'Meetup DevOps & CI/CD',
                'description' => 'Rencontre autour des pratiques DevOps et de l\'intégration continue.',
                'dateStart' => new \DateTime('+18 days 19:00'),
                'dateEnd' => new \DateTime('+18 days 21:30'),
                'address' => '7 Place Bellecour',
                'postalCode' => '69002',
                'city' => 'Lyon',
                'country' => 'France',
                'organizer' => 'DevOps Lyon',
                'maxParticipants' => 40,
                'categoryIndex' => 3,
            ],
        ];

        $events = [];
        $admin = $users['admin'];

        foreach ($eventsData as $eventData) {
            $event = new Event();
            $event->setTitle($eventData['title']);
            $event->setDescription($eventData['description']);
            $event->setDateStart($eventData['dateStart']);
            $event->setDateEnd($eventData['dateEnd']);
            $event->setAddress($eventData['address']);
            $event->setPostalCode($eventData['postalCode']);
            $event->setCity($eventData['city']);
            $event->setCountry($eventData['country']);
            $event->setOrganizer($eventData['organizer']);
            $event->setMaxParticipants($eventData['maxParticipants']);
            $event->setCategory($categories[$eventData['categoryIndex']]);
            $event->setCreatedBy($admin);
            $event->computeSlug($this->slugger);

            $manager->persist($event);
            $events[] = $event;
        }

        return $events;
    }

    private function createRegistrations(ObjectManager $manager, array $users, array $events): void
    {
        // On retire l'admin du tableau des utilisateurs standards
        $standardUsers = array_slice($users, 1);

        // Pour chaque événement, on inscrit un nombre aléatoire d'utilisateurs
        foreach ($events as $event) {
            // Nombre aléatoire d'inscriptions (entre 5 et 15, ou le max de participants si défini)
            $maxRegistrations = $event->getMaxParticipants()
                ? min(15, $event->getMaxParticipants())
                : 15;

            $registrationCount = rand(5, $maxRegistrations);

            // Mélanger les utilisateurs et en prendre un certain nombre
            $shuffledUsers = $standardUsers;
            shuffle($shuffledUsers);
            $participatingUsers = array_slice($shuffledUsers, 0, min($registrationCount, count($shuffledUsers)));

            foreach ($participatingUsers as $user) {
                $registration = new ToRegister();
                $registration->setUser($user);
                $registration->setEvent($event);
                $registration->setStatus('confirmed');

                // Date d'inscription aléatoire entre 1 et 30 jours avant l'événement
                $daysBeforeEvent = rand(1, 30);
                $registrationDate = (clone $event->getDateStart())->modify("-{$daysBeforeEvent} days");
                $registration->setRegisteredAt($registrationDate);

                $manager->persist($registration);
            }
        }
    }
}
