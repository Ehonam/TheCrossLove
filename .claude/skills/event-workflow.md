# Skill: Event Workflow

Workflow complet pour creer une nouvelle fonctionnalite liee aux evenements.

## Vue d'ensemble

```
┌──────────┐     ┌────────────┐     ┌──────────┐
│   User   │────<│ ToRegister │>────│  Event   │
└──────────┘     └────────────┘     └──────────┘
                                          │
                                    ┌──────────┐
                                    │ Category │
                                    └──────────┘
```

## Etapes du workflow

### 1. Creer/Modifier l'Entity

**Fichier**: `src/Entity/Event.php`

```php
// Ajouter un champ
#[ORM\Column(length: 255, nullable: true)]
private ?string $newField = null;

// Getter
public function getNewField(): ?string
{
    return $this->newField;
}

// Setter
public function setNewField(?string $newField): static
{
    $this->newField = $newField;
    return $this;
}
```

### 2. Generer la migration

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### 3. Creer/Modifier le FormType

**Fichier**: `src/Form/EventType.php`

```php
$builder
    ->add('newField', TextType::class, [
        'label' => 'Nouveau champ',
        'required' => false,
        'attr' => ['class' => 'form-control'],
    ]);
```

### 4. Modifier le Controller

**Fichier**: `src/Controller/EventController.php`

```php
#[Route('/event/new-action', name: 'app_event_new_action')]
public function newAction(Request $request): Response
{
    // Logique ici
}
```

### 5. Creer le Template Twig

**Fichier**: `templates/event/new_action.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Nouvelle Action{% endblock %}

{% block body %}
<div class="container">
    <h1>Nouvelle Action</h1>
    {{ form_start(form) }}
        {{ form_widget(form) }}
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    {{ form_end(form) }}
</div>
{% endblock %}
```

### 6. Creer les Tests

**Test unitaire**: `tests/Unit/Entity/EventTest.php`

```php
public function testNewField(): void
{
    $event = new Event();
    $event->setNewField('valeur');

    $this->assertEquals('valeur', $event->getNewField());
}
```

**Test integration**: `tests/Integration/Controller/EventControllerTest.php`

```php
public function testNewActionPage(): void
{
    $client = static::createClient();
    $client->request('GET', '/event/new-action');

    $this->assertResponseIsSuccessful();
}
```

## Checklist

- [ ] Entity modifiee avec validation
- [ ] Migration generee et executee
- [ ] FormType mis a jour
- [ ] Controller avec nouvelle route
- [ ] Template Twig cree
- [ ] Test unitaire ajoute
- [ ] Test d'integration ajoute
- [ ] `php bin/phpunit` passe
- [ ] `php bin/console lint:container` OK
- [ ] `php bin/console doctrine:schema:validate` OK

## Commandes rapides

```bash
# Workflow complet
php bin/console make:migration && \
php bin/console doctrine:migrations:migrate --no-interaction && \
php bin/phpunit && \
php bin/console lint:container && \
php bin/console doctrine:schema:validate
```

## Points d'attention

1. **ID convention**: `id_event` pas `id`
2. **Timestamps**: `createdAt`, `updatedAt` avec PreUpdate
3. **Relations**: Configurer cascade correctement
4. **Validation**: Ajouter Assert constraints
5. **CSRF**: Jamais desactiver
6. **Tests**: Toujours tester avant commit
