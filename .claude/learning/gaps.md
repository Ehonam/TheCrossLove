# Learning Gaps - Spaced Repetition

Sujets a reviser regulierement pour maitriser le projet TheCrossLove.

## Doctrine ORM

### A reviser frequemment
- [ ] Relations ManyToOne / OneToMany
- [ ] Cascade persist vs remove
- [ ] Lifecycle callbacks (PreUpdate, PrePersist)
- [ ] DQL vs QueryBuilder
- [ ] Lazy loading vs Eager fetching

### Patterns importants
```php
// Relation avec cascade
#[ORM\OneToMany(targetEntity: ToRegister::class, mappedBy: 'event', cascade: ['remove'])]

// Lifecycle callback
#[ORM\PreUpdate]
public function setUpdatedAtValue(): void
```

## Symfony Security

### A reviser frequemment
- [ ] Hierarchie des roles
- [ ] Access control dans security.yaml
- [ ] Voters pour permissions complexes
- [ ] CSRF protection
- [ ] Password hashing

### Patterns importants
```php
// Verification dans controller
$this->denyAccessUnlessGranted('ROLE_ADMIN');

// Verification ownership
if ($event->getCreatedBy() !== $this->getUser()) {
    throw $this->createAccessDeniedException();
}
```

## Formulaires Symfony

### A reviser frequemment
- [ ] FormType et options
- [ ] Validation constraints
- [ ] Groupes de validation
- [ ] Transformation de donnees
- [ ] File upload

### Patterns importants
```php
// Form handling
$form = $this->createForm(EventType::class, $event);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    // ...
}
```

## Tests PHPUnit

### A reviser frequemment
- [ ] Tests unitaires vs integration
- [ ] Fixtures et DataFixtures
- [ ] Mock et Stub
- [ ] Assertions Symfony
- [ ] WebTestCase

### Patterns importants
```php
// Test controller
$client = static::createClient();
$client->request('GET', '/event/');
$this->assertResponseIsSuccessful();

// Test avec auth
$client->loginUser($user);
```

## Twig Templates

### A reviser frequemment
- [ ] Extends et blocks
- [ ] Include et embed
- [ ] Macros
- [ ] Filtres et fonctions
- [ ] Path et asset

### Patterns importants
```twig
{% extends 'base.html.twig' %}

{% block body %}
    {{ path('app_event_show', {'id': event.id}) }}
{% endblock %}
```

---

## Planning de revision

| Semaine | Sujet principal | Points specifiques |
|---------|-----------------|-------------------|
| 1 | Doctrine | Relations, Cascade |
| 2 | Security | Roles, CSRF |
| 3 | Forms | Validation, Upload |
| 4 | Tests | Unit, Integration |
| 5 | Twig | Templates, Macros |
| 6 | Revision generale | Tous les sujets |

## Ressources

- [Symfony Docs](https://symfony.com/doc/current/index.html)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)
- [PHPUnit](https://phpunit.de/documentation.html)

## Notes personnelles

_Ajouter ici les points difficiles rencontres pendant le developpement_

### Erreurs courantes commises
1. ...
2. ...

### Solutions trouvees
1. ...
2. ...
