# Notes: Domaine Testing

## Structure des tests

```
tests/
├── Unit/
│   ├── Entity/
│   │   ├── EventTest.php
│   │   ├── UserTest.php
│   │   └── ToRegisterTest.php
│   └── Repository/
└── Integration/
    └── Controller/
        ├── EventControllerTest.php
        └── SecurityControllerTest.php
```

## Tests Unitaires vs Integration

| Type | Localisation | But | Base de donnees |
|------|--------------|-----|-----------------|
| Unit | tests/Unit/ | Logique metier isolee | Non |
| Integration | tests/Integration/ | Flux complet HTTP | Oui (test DB) |

## Commandes de test

```bash
# Tous les tests
php bin/phpunit

# Tests unitaires uniquement
php bin/phpunit tests/Unit/

# Tests d'integration uniquement
php bin/phpunit tests/Integration/

# Un fichier specifique
php bin/phpunit tests/Unit/Entity/EventTest.php

# Avec couverture
php bin/phpunit --coverage-html=var/coverage
```

## DatabaseTestTrait

Utiliser pour l'isolation des tests avec base de donnees.

```php
use App\Tests\DatabaseTestTrait;

class MyControllerTest extends WebTestCase
{
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetDatabase();
    }
}
```

## Configuration test (.env.test)

```env
DATABASE_URL="mysql://root:password@127.0.0.1:3306/thecrosslove_test"
```

**IMPORTANT**: Base de donnees separee pour les tests!

## Patterns de test Entity

### Test des methodes metier
```php
class EventTest extends TestCase
{
    public function testIsUpcoming(): void
    {
        $event = new Event();
        $event->setDateStart(new \DateTime('+1 day'));

        $this->assertTrue($event->isUpcoming());
    }

    public function testIsFull(): void
    {
        $event = new Event();
        $event->setMaxParticipants(2);

        // Ajouter 2 inscriptions
        $reg1 = new ToRegister();
        $reg2 = new ToRegister();
        $event->addRegistration($reg1);
        $event->addRegistration($reg2);

        $this->assertTrue($event->isFull());
    }
}
```

### Test des validations
```php
public function testTitleTooShort(): void
{
    $event = new Event();
    $event->setTitle('Ab'); // < 5 chars

    $validator = Validation::createValidator();
    $violations = $validator->validate($event);

    $this->assertGreaterThan(0, count($violations));
}
```

## Patterns de test Controller

### Test GET simple
```php
public function testEventListPage(): void
{
    $client = static::createClient();
    $client->request('GET', '/event/');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorExists('.event-list');
}
```

### Test avec authentification
```php
public function testAdminDashboardRequiresAdmin(): void
{
    $client = static::createClient();

    // Sans auth
    $client->request('GET', '/admin/');
    $this->assertResponseRedirects('/login');

    // Avec user normal
    $user = $this->createUser(['ROLE_USER']);
    $client->loginUser($user);
    $client->request('GET', '/admin/');
    $this->assertResponseStatusCodeSame(403);

    // Avec admin
    $admin = $this->createUser(['ROLE_ADMIN']);
    $client->loginUser($admin);
    $client->request('GET', '/admin/');
    $this->assertResponseIsSuccessful();
}
```

### Test de formulaire
```php
public function testEventCreation(): void
{
    $client = static::createClient();
    $admin = $this->getAdminUser();
    $client->loginUser($admin);

    $crawler = $client->request('GET', '/admin/events/new');

    $form = $crawler->selectButton('Creer')->form([
        'event[title]' => 'Mon evenement test',
        'event[description]' => 'Une description suffisamment longue',
        // ... autres champs
    ]);

    $client->submit($form);
    $this->assertResponseRedirects('/admin/events');
}
```

## Fixtures pour les tests

```php
// DataFixtures/AppFixtures.php
public function load(ObjectManager $manager): void
{
    // Admin
    $admin = new User();
    $admin->setEmail('admin@thecrosslove.com');
    $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
    $admin->setRoles(['ROLE_ADMIN']);
    $manager->persist($admin);

    // User standard
    $user = new User();
    $user->setEmail('john.doe@example.com');
    // ...
}
```

## Verification post-code

Apres chaque modification:
```bash
# 1. Tests passent
php bin/phpunit

# 2. Container valide
php bin/console lint:container

# 3. Schema DB sync
php bin/console doctrine:schema:validate

# 4. Health check
curl localhost:8000/health
```

## Erreurs frequentes a eviter

1. **Utiliser la DB de prod** - Toujours .env.test
2. **Oublier setUp/tearDown** - Isolation des tests
3. **Tests dependants** - Chaque test doit etre independant
4. **Fixtures non chargees** - `--no-interaction` en CI
5. **Assertions manquantes** - Toujours asserter quelque chose
