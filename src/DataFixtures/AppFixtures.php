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

        // Utilisateur admin avec mot de passe ANSSI niveau maximal (25+ caractères)
        $admin = new User();
        $admin->setEmail('admin@thecrosslove.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('Système');
        $admin->setRoles(['ROLE_ADMIN']);
        // Mot de passe conforme ANSSI niveau maximal : 32 caractères
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Th3Cr0ss!L0v3@2026#Adm1n$Secure!'));
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
            ['email' => 'koffi@gmail.com', 'firstName' => 'Koffi', 'lastName' => 'Hamenou'],
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
        // Catégories adaptées aux événements humanitaires (Sénégal & RDC)
        $categoriesData = [
            'Conférences',      // 0
            'Ateliers',         // 1
            'Sensibilisation',  // 2
            'Humanitaire',      // 3
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
            // === ÉVÉNEMENTS HUMANITAIRES - SÉNÉGAL (Fatick) ===
            [
                'title' => 'Sensibilisation Protection des Enfants Talibés - Fatick',
                'description' => 'Événement de sensibilisation communautaire pour protéger les enfants talibés contre la mendicité forcée. Animation théâtre participatif avec des scènes éducatives sur les droits des enfants, discussions en groupes avec leaders locaux (imams, chefs de village, enseignants). Distribution de kits scolaires et d\'hygiène. Partenariat avec Village Pilote et Secours Islamique France (SIF). Thème : "Protéger nos enfants localement, favoriser l\'éducation". Objectif : sensibiliser 150 familles rurales aux risques de la mendicité et promouvoir les daaras modernes.',
                'dateStart' => new \DateTime('+25 days 09:00'),
                'dateEnd' => new \DateTime('+25 days 17:00'),
                'address' => 'Centre Communautaire de Fatick',
                'postalCode' => 'FK001',
                'city' => 'Fatick',
                'country' => 'Sénégal',
                'organizer' => 'TheCrossLove & Village Pilote',
                'maxParticipants' => 200,
                'categoryIndex' => 2, // Sensibilisation
            ],
            [
                'title' => 'Atelier Éducatif pour les Daaras Modernes - Région Fatick',
                'description' => 'Atelier de formation pour les marabouts et enseignants coraniques sur l\'intégration de l\'éducation formelle dans les daaras. Sessions pratiques : enseignement du Coran + mathématiques/français, hygiène et santé des enfants, méthodes pédagogiques modernes. Implication des imams progressistes et leaders communautaires. Distribution de matériel éducatif (tableaux, manuels, fournitures). En collaboration avec Save the Children et PADEM. Objectif : moderniser 10 daaras de la région.',
                'dateStart' => new \DateTime('+40 days 08:30'),
                'dateEnd' => new \DateTime('+41 days 16:00'),
                'address' => 'Mosquée Centrale et École Coranique Al-Falah',
                'postalCode' => 'FK002',
                'city' => 'Fatick',
                'country' => 'Sénégal',
                'organizer' => 'TheCrossLove & Save the Children',
                'maxParticipants' => 80,
                'categoryIndex' => 1, // Ateliers
            ],
            [
                'title' => 'Journée des Droits de l\'Enfant - Villages de Fatick',
                'description' => 'Grande journée de célébration et sensibilisation dans plusieurs villages de la région de Fatick. Programme : théâtre interactif sur les droits de l\'enfant (joué par des enfants et animateurs locaux), jeux éducatifs sur l\'importance de l\'école, témoignages d\'anciens talibés réinsérés, chants et danses traditionnels. Distribution de moustiquaires, kits d\'hygiène et repas. Coordination via groupes WhatsApp avec leaders locaux. Partenariat Anti-Slavery International et UNICEF Sénégal. Suivi prévu : création de comités villageois de protection de l\'enfance.',
                'dateStart' => new \DateTime('+55 days 10:00'),
                'dateEnd' => new \DateTime('+55 days 18:00'),
                'address' => 'Place du Village de Diakhao',
                'postalCode' => 'FK003',
                'city' => 'Diakhao (Fatick)',
                'country' => 'Sénégal',
                'organizer' => 'TheCrossLove & UNICEF Sénégal',
                'maxParticipants' => 300,
                'categoryIndex' => 3, // Humanitaire
            ],
            [
                'title' => 'Formation des Femmes Leaders - Prévention Mendicité Fatick',
                'description' => 'Programme de formation destiné aux femmes leaders et mères de famille de la région de Fatick. Objectif : les outiller pour identifier les risques de la mendicité forcée et protéger leurs enfants. Sessions : reconnaissance des signes d\'exploitation, droits des enfants selon la loi sénégalaise, alternatives économiques (AGR - Activités Génératrices de Revenus), témoignages de familles ayant récupéré leurs enfants des daaras exploiteurs. Distribution de microcrédits pour démarrer des activités. En partenariat avec ONU Femmes et Pour une Enfance au Sénégal. Suivi : création d\'un réseau de femmes vigilantes.',
                'dateStart' => new \DateTime('+70 days 09:00'),
                'dateEnd' => new \DateTime('+71 days 16:00'),
                'address' => 'Centre des Femmes de Fatick',
                'postalCode' => 'FK004',
                'city' => 'Fatick',
                'country' => 'Sénégal',
                'organizer' => 'TheCrossLove & ONU Femmes',
                'maxParticipants' => 120,
                'categoryIndex' => 1, // Ateliers
            ],
            [
                'title' => 'Caravane Santé-Éducation - Villages Ruraux Fatick',
                'description' => 'Caravane mobile visitant 5 villages reculés de la région de Fatick sur 3 jours. Services offerts : consultations médicales gratuites pour enfants (vaccination, dépistage malnutrition), sensibilisation des parents sur l\'importance de l\'éducation formelle vs mendicité, inscription des enfants non scolarisés, distribution de kits scolaires et médicaments. Équipe : médecins bénévoles, enseignants, travailleurs sociaux, animateurs. Partenariat Médecins du Monde et Ministère de l\'Éducation du Sénégal. Coordination via WhatsApp avec chefs de village.',
                'dateStart' => new \DateTime('+85 days 08:00'),
                'dateEnd' => new \DateTime('+87 days 18:00'),
                'address' => 'Départ : Préfecture de Fatick',
                'postalCode' => 'FK005',
                'city' => 'Fatick (tournée rurale)',
                'country' => 'Sénégal',
                'organizer' => 'TheCrossLove & Médecins du Monde',
                'maxParticipants' => 500,
                'categoryIndex' => 3, // Humanitaire
            ],
            // === ÉVÉNEMENTS HUMANITAIRES - RDC (Bukavu) ===
            [
                'title' => 'Soutien aux Survivantes de Violences - Bukavu',
                'description' => 'Événement de sensibilisation et soutien psycho-social pour les filles et femmes victimes de violences sexuelles au Sud Kivu. En partenariat avec la Fondation Panzi du Dr. Denis Mukwege (Prix Nobel de la Paix 2018). Programme : ateliers psycho-sociaux (thérapie de groupe, témoignages anonymes de survivantes pour briser les tabous), sessions d\'information sur les droits et l\'accès aux soins à l\'Hôpital Panzi, formations à la réinsertion socio-économique (couture, petit commerce). Distribution de kits dignité (hygiéniques) et soutien nutritionnel. Objectif : accompagner 100 survivantes vers la reconstruction. Sécurité assurée via ONG partenaires.',
                'dateStart' => new \DateTime('+35 days 09:00'),
                'dateEnd' => new \DateTime('+36 days 17:00'),
                'address' => 'Centre Panzi - Avenue Panzi',
                'postalCode' => 'BKV01',
                'city' => 'Bukavu',
                'country' => 'République Démocratique du Congo',
                'organizer' => 'TheCrossLove & Fondation Panzi',
                'maxParticipants' => 150,
                'categoryIndex' => 3, // Humanitaire
            ],
            [
                'title' => 'Protection des Orphelins du Kivu - Camp de Déplacés Bukavu',
                'description' => 'Journée de sensibilisation et distribution pour les enfants orphelins dans les camps de déplacés autour de Bukavu (Sud Kivu). En collaboration avec UNICEF RDC et Croix-Rouge (ICRC). Activités : animations ludiques et psycho-sociales pour les enfants (5-18 ans), sensibilisation des communautés à l\'adoption/intégration communautaire des orphelins (contre la stigmatisation), éducation sur les droits des enfants et l\'accès à l\'école/santé. Distribution : kits nutritionnels, fournitures scolaires, vêtements, moustiquaires. Formation de comités communautaires de protection de l\'enfance pour durabilité. Coordination sécurisée via WhatsApp avec équipes ONG. Objectif : toucher 200 enfants et 100 familles d\'accueil potentielles.',
                'dateStart' => new \DateTime('+50 days 08:00'),
                'dateEnd' => new \DateTime('+51 days 16:00'),
                'address' => 'Camp de Déplacés de Kavumu',
                'postalCode' => 'BKV02',
                'city' => 'Kavumu (Bukavu)',
                'country' => 'République Démocratique du Congo',
                'organizer' => 'TheCrossLove & UNICEF RDC',
                'maxParticipants' => 250,
                'categoryIndex' => 2, // Sensibilisation
            ],
            [
                'title' => 'Formation Réinsertion Économique - Survivantes Bukavu',
                'description' => 'Programme de formation professionnelle sur 2 jours pour les femmes et filles survivantes de violences sexuelles au Sud Kivu. En partenariat avec la Fondation Panzi et Women for Women International. Ateliers pratiques : couture et confection textile, fabrication de savon artisanal, petit commerce et gestion financière, agriculture maraîchère. Chaque participante reçoit un kit de démarrage (machine à coudre, outils, capital initial). Suivi post-formation pendant 6 mois. Objectif : autonomiser 80 femmes économiquement pour briser le cycle de vulnérabilité. Sécurité et transport assurés.',
                'dateStart' => new \DateTime('+65 days 08:00'),
                'dateEnd' => new \DateTime('+66 days 17:00'),
                'address' => 'Centre de Formation Panzi',
                'postalCode' => 'BKV03',
                'city' => 'Bukavu',
                'country' => 'République Démocratique du Congo',
                'organizer' => 'TheCrossLove & Women for Women International',
                'maxParticipants' => 80,
                'categoryIndex' => 1, // Ateliers
            ],
            [
                'title' => 'Théâtre Communautaire - Prévention Violences Kivu',
                'description' => 'Tournée de théâtre communautaire dans 3 villages du Sud Kivu pour sensibiliser à la prévention des violences sexuelles et à la protection des enfants. Troupe locale formée par Search for Common Ground. Pièces jouées en swahili et langues locales abordant : consentement, signalement des violences, rôle des hommes dans la protection, droits des enfants orphelins. Discussions post-spectacle avec leaders communautaires et groupes de femmes. Distribution de dépliants informatifs et numéros d\'urgence. Partenariat MSF et MONUSCO. Objectif : toucher 1000 personnes sur 3 jours.',
                'dateStart' => new \DateTime('+80 days 14:00'),
                'dateEnd' => new \DateTime('+82 days 20:00'),
                'address' => 'Villages de Walungu, Kabare, Kalehe',
                'postalCode' => 'BKV04',
                'city' => 'Sud Kivu (tournée)',
                'country' => 'République Démocratique du Congo',
                'organizer' => 'TheCrossLove & Search for Common Ground',
                'maxParticipants' => 400,
                'categoryIndex' => 2, // Sensibilisation
            ],
            [
                'title' => 'Conférence Internationale - Droits des Enfants du Kivu',
                'description' => 'Conférence réunissant ONG, représentants gouvernementaux, chercheurs et survivantes pour dresser un bilan et définir des actions concrètes pour les enfants du Kivu. Programme : état des lieux 2026 (UNICEF, HRW), témoignages de survivantes (anonymes), présentation des projets réussis (Panzi, ICRC), ateliers de travail sur la protection, plaidoyer pour renforcement législatif congolais. Diffusion en direct via les réseaux sociaux. Rédaction d\'une déclaration commune. Partenaires : Fondation Mukwege, UNICEF, Amnesty International, Union Européenne. Lieu sécurisé avec accréditation obligatoire.',
                'dateStart' => new \DateTime('+95 days 09:00'),
                'dateEnd' => new \DateTime('+95 days 18:00'),
                'address' => 'Hôtel Orchid Safari - Salle de Conférence',
                'postalCode' => 'BKV05',
                'city' => 'Bukavu',
                'country' => 'République Démocratique du Congo',
                'organizer' => 'TheCrossLove & Fondation Mukwege',
                'maxParticipants' => 200,
                'categoryIndex' => 0, // Conférences
                'status' => 'active',
            ],
            // === ÉVÉNEMENT PASSÉ - SÉNÉGAL ===
            [
                'title' => 'Formation Initiale des Animateurs Communautaires - Fatick',
                'description' => 'Première session de formation des animateurs communautaires qui interviendront dans les villages de la région de Fatick. Programme réalisé : techniques d\'animation participative, droits de l\'enfant et cadre légal sénégalais, identification des enfants en situation de mendicité, médiation avec les familles et marabouts, premiers secours et hygiène de base. 25 animateurs formés issus de 15 villages différents. Remise de certificats et kits d\'animation. Partenariat avec le Ministère de la Famille et Village Pilote. Événement réussi avec 100% de participation.',
                'dateStart' => new \DateTime('-45 days 08:00'),
                'dateEnd' => new \DateTime('-43 days 17:00'),
                'address' => 'Centre de Formation Régional de Fatick',
                'postalCode' => 'FK006',
                'city' => 'Fatick',
                'country' => 'Sénégal',
                'organizer' => 'TheCrossLove & Ministère de la Famille',
                'maxParticipants' => 30,
                'categoryIndex' => 1, // Ateliers
                'status' => 'active',
            ],
            // === ÉVÉNEMENT ANNULÉ - RDC (Goma) ===
            [
                'title' => '[ANNULÉ] Sensibilisation Protection des Enfants - Camp de Mugunga (Goma)',
                'description' => 'ÉVÉNEMENT ANNULÉ EN RAISON DE L\'INSÉCURITÉ DANS LA RÉGION. Cet événement de sensibilisation était prévu dans le camp de déplacés de Mugunga près de Goma (Nord Kivu). Programme initialement prévu : sensibilisation des familles déplacées à la protection des enfants, distribution de kits nutritionnels et scolaires, animations psycho-sociales pour les enfants traumatisés, coordination avec UNHCR et Croix-Rouge. L\'événement a été annulé suite aux affrontements armés dans la zone. Une reprogrammation sera envisagée dès que les conditions de sécurité le permettront. Les équipes et les ressources ont été réaffectées à d\'autres sites sécurisés.',
                'dateStart' => new \DateTime('+30 days 09:00'),
                'dateEnd' => new \DateTime('+31 days 17:00'),
                'address' => 'Camp de Déplacés de Mugunga',
                'postalCode' => 'GOM01',
                'city' => 'Goma',
                'country' => 'République Démocratique du Congo',
                'organizer' => 'TheCrossLove & UNHCR',
                'maxParticipants' => 300,
                'categoryIndex' => 2, // Sensibilisation
                'status' => 'cancelled',
            ],
            // === SOUTENANCE CDA - Koffi Hamenou ===
            [
                'title' => 'Soutenance CDA - Koffi Hamenou - Présentation appli humanitaire TheCrossLove',
                'description' => 'Soutenance du titre professionnel Concepteur Développeur d\'Applications de Koffi Hamenou. Présentation de l\'application TheCrossLove, une plateforme de gestion d\'événements humanitaires. Démonstration des fonctionnalités développées avec Symfony 7.3, PHP 8.2, Doctrine ORM et MySQL 8.0. Cette application permet aux utilisateurs de créer et gérer des événements humanitaires, de s\'inscrire aux événements, et aux administrateurs de gérer les participants et les catégories d\'événements. Architecture MVC complète avec système d\'authentification, gestion des rôles, et intégration de cartes interactives.',
                'dateStart' => new \DateTime('2026-02-01 21:00'),
                'dateEnd' => new \DateTime('2026-02-04 17:00'),
                'address' => 'M2I Formation',
                'postalCode' => '67300',
                'city' => 'Schiltigheim',
                'country' => 'France',
                'organizer' => 'M2I Formation Strasbourg',
                'maxParticipants' => 20,
                'categoryIndex' => 0, // Conférences
                'status' => 'active',
                'image' => 'soutenance-cda.jpg',
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
            $event->setStatus($eventData['status'] ?? 'active');
            if (isset($eventData['image'])) {
                $event->setImage($eventData['image']);
            }
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

        // Trouver l'utilisateur Koffi et l'événement de soutenance
        $koffiUser = null;
        $soutenanceEvent = null;

        foreach ($standardUsers as $user) {
            if ($user->getEmail() === 'koffi@gmail.com') {
                $koffiUser = $user;
                break;
            }
        }

        foreach ($events as $event) {
            if (str_contains($event->getTitle(), 'Soutenance CDA - Koffi Hamenou')) {
                $soutenanceEvent = $event;
                break;
            }
        }

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

        // Inscription spéciale de Koffi à l'événement de soutenance avec coordonnées GPS
        if ($koffiUser && $soutenanceEvent) {
            $koffiRegistration = new ToRegister();
            $koffiRegistration->setUser($koffiUser);
            $koffiRegistration->setEvent($soutenanceEvent);
            $koffiRegistration->setStatus('confirmed');
            $koffiRegistration->setWhatsappNumber('+33641154337');
            // Coordonnées GPS de M2I Schiltigheim
            $koffiRegistration->setLatitude('48.6159');
            $koffiRegistration->setLongitude('7.7458');
            $koffiRegistration->setLocationUpdatedAt(new \DateTime());
            $koffiRegistration->setRegisteredAt(new \DateTime());

            $manager->persist($koffiRegistration);
        }
    }
}
