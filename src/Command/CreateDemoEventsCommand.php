<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-demo-events',
    description: 'Crée 7 événements de démonstration',
)]
class CreateDemoEventsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //  ÉTAPE 1 : Récupérer ou créer un utilisateur admin
        $admin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@eventhub.com']);

        if (!$admin) {
            $io->warning('Aucun utilisateur admin trouvé. Création d\'un compte admin...');
            $admin = new User();
            $admin->setEmail('admin@eventhub.com');
            $admin->setPassword('$2y$13$dummypasswordhash');
            $admin->setFirstName('Admin');
            $admin->setLastName('EventHub');
            $admin->setRoles(['ROLE_ADMIN']);
            $this->entityManager->persist($admin);
            $this->entityManager->flush();
            $io->success('Utilisateur admin créé : admin@eventhub.com');
        }

        //  ÉTAPE 2 : Créer les catégories si elles n'existent pas
        $categories = ['Conference', 'Workshop', 'Seminar', 'Formation', 'Hackathon', 'Meetup'];
        $categoryEntities = [];

        foreach ($categories as $catName) {
            $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $catName]);
            if (!$category) {
                $category = new Category();
                $category->setName($catName);
                $category->setSlug(strtolower($catName));
                $this->entityManager->persist($category);
            }
            $categoryEntities[$catName] = $category;
        }
        $this->entityManager->flush();

        //  ÉTAPE 3 : Créer les 7 événements
        $events = [
            [
                'title' => 'Conférence Leadership & Innovation 2025',
                'category' => 'Conference',
                'description' => "Une conférence exceptionnelle réunissant les leaders de demain. Venez découvrir les
  nouvelles tendances en management, leadership et innovation. Des intervenants de renommée internationale
  partageront leur expérience et leur vision du futur.\n\nAu programme :\n- Keynotes inspirants\n- Tables rondes
  interactives\n- Networking avec les participants\n- Ateliers pratiques",
                'startDate' => new \DateTime('+15 days 09:00'),
                'endDate' => new \DateTime('+15 days 18:00'),
                'address' => '123 Avenue des Champs-Élysées',
                'postalCode' => '75008',
                'city' => 'Paris',
                'country' => 'France',
                'organizer' => 'EventHub Formation',
                'maxParticipants' => 200,
            ],
            [
                'title' => 'Atelier Développement Personnel',
                'category' => 'Workshop',
                'description' => "Un atelier intensif de développement personnel pour découvrir et libérer votre plein
   potentiel. Apprenez à gérer votre stress, à développer votre confiance en vous et à atteindre vos
  objectifs.\n\nCe que vous allez apprendre :\n- Techniques de gestion du stress\n- Développement de la confiance en
   soi\n- Définition et atteinte d'objectifs\n- Communication assertive",
                'startDate' => new \DateTime('+8 days 14:00'),
                'endDate' => new \DateTime('+8 days 17:00'),
                'address' => '45 Rue de la République',
                'postalCode' => '69002',
                'city' => 'Lyon',
                'country' => 'France',
                'organizer' => 'Coach & Vous',
                'maxParticipants' => 30,
            ],
            [
                'title' => 'Séminaire Entrepreneuriat Digital',
                'category' => 'Seminar',
                'description' => "Découvrez les clés pour réussir dans l'entrepreneuriat digital. De l'idée au
  lancement, en passant par le financement et la croissance, ce séminaire vous donnera tous les outils
  nécessaires.\n\nThématiques abordées :\n- Business model digital\n- Stratégies de croissance\n- Levée de fonds\n-
  Marketing digital\n- Outils technologiques",
                'startDate' => new \DateTime('+20 days 09:00'),
                'endDate' => new \DateTime('+21 days 17:00'),
                'address' => '78 La Canebière',
                'postalCode' => '13001',
                'city' => 'Marseille',
                'country' => 'France',
                'organizer' => 'Startup Academy',
                'maxParticipants' => 100,
            ],
            [
                'title' => 'Formation Gestion Projet Agile',
                'category' => 'Formation',
                'description' => "Formation certifiante aux méthodologies Agile et Scrum. Apprenez à gérer vos projets
   de manière itérative et collaborative.\n\nObjectifs :\n- Maîtriser les principes Agile\n- Comprendre le framework
   Scrum\n- Gérer efficacement une équipe\n- Utiliser les outils Agile\n- Certification Scrum Master incluse",
                'startDate' => new \DateTime('+12 days 09:00'),
                'endDate' => new \DateTime('+14 days 17:00'),
                'address' => '34 Boulevard Victor Hugo',
                'postalCode' => '31000',
                'city' => 'Toulouse',
                'country' => 'France',
                'organizer' => 'Agile Institute',
                'maxParticipants' => 25,
            ],
            [
                'title' => 'Hackathon Tech for Good 48h',
                'category' => 'Hackathon',
                'description' => "Un hackathon de 48h pour développer des solutions technologiques au service du bien
  commun. Rejoignez des équipes pluridisciplinaires.\n\nChallenges :\n- Environnement & Climat\n- Éducation &
  Formation\n- Santé & Bien-être\n- Inclusion sociale\n\nPrix à gagner : 10 000€",
                'startDate' => new \DateTime('+25 days 18:00'),
                'endDate' => new \DateTime('+27 days 18:00'),
                'address' => '56 Quai de Bacalan',
                'postalCode' => '33000',
                'city' => 'Bordeaux',
                'country' => 'France',
                'organizer' => 'Tech For Good Foundation',
                'maxParticipants' => 150,
            ],
            [
                'title' => 'Conférence IA & Éthique',
                'category' => 'Conference',
                'description' => "Une conférence sur les enjeux éthiques de l'intelligence artificielle. Experts,
  chercheurs et entrepreneurs débattront des impacts de l'IA.\n\nIntervenants :\n- Chercheurs en IA\n- Entrepreneurs
   tech\n- Philosophes\n- Juristes spécialisés\n\nThèmes : Biais algorithmiques, Protection données, IA
  responsable",
                'startDate' => new \DateTime('+30 days 09:00'),
                'endDate' => new \DateTime('+30 days 18:00'),
                'address' => '12 Place de la République',
                'postalCode' => '67000',
                'city' => 'Strasbourg',
                'country' => 'France',
                'organizer' => 'AI Ethics Forum',
                'maxParticipants' => 300,
            ],
            [
                'title' => 'Meetup Networking Pro Lille',
                'category' => 'Meetup',
                'description' => "Un événement networking convivial pour développer votre réseau professionnel.
  Rencontrez entrepreneurs, freelances et cadres.\n\nAu programme :\n- Speed networking\n- Pitchs de 3 minutes\n-
  Apéritif networking\n- Échanges informels\n\nGratuit - Tous secteurs",
                'startDate' => new \DateTime('+5 days 18:30'),
                'endDate' => new \DateTime('+5 days 22:00'),
                'address' => '89 Rue Nationale',
                'postalCode' => '59000',
                'city' => 'Lille',
                'country' => 'France',
                'organizer' => 'Lille Business Network',
                'maxParticipants' => null,
            ],
        ];

        $io->progressStart(count($events));

        foreach ($events as $eventData) {
            $event = new Event();
            $event->setTitle($eventData['title']);

            // Générer le slug
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $eventData['title']), '-'));
            $event->setSlug($slug . '-' . uniqid());

            $event->setDescription($eventData['description']);
            $event->setDateStart($eventData['startDate']);
            $event->setDateEnd($eventData['endDate']);
            $event->setAddress($eventData['address']);
            $event->setPostalCode($eventData['postalCode']);
            $event->setCity($eventData['city']);
            $event->setCountry($eventData['country']);
            $event->setOrganizer($eventData['organizer']);
            $event->setMaxParticipants($eventData['maxParticipants']);
            $event->setCreatedBy($admin);
            $event->setCategory($categoryEntities[$eventData['category']]);

            $this->entityManager->persist($event);
            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();

        $io->success('7 événements de démonstration ont été créés avec succès !');
        $io->table(
            ['Ville', 'Événement', 'Date', 'Places'],
            array_map(fn($e) => [
                $e['city'],
                $e['title'],
                $e['startDate']->format('d/m/Y'),
                $e['maxParticipants'] ?? 'Illimité'
            ], $events)
        );

        return Command::SUCCESS;
    }

}
