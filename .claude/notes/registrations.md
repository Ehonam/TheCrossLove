# Notes: Domaine Registrations

## Entity ToRegister (src/Entity/ToRegister.php)

### Champs principaux
| Champ | Type | Contraintes |
|-------|------|-------------|
| id | int | `id_registration` |
| user | User | ManyToOne, NOT NULL |
| event | Event | ManyToOne, NOT NULL, CASCADE DELETE |
| registeredAt | DateTime | Auto-set au construct |
| status | string(20) | Default 'confirmed' |
| whatsappNumber | string(20) | Regex: +?[0-9]{10,15} |
| latitude | decimal(10,7) | Nullable |
| longitude | decimal(10,7) | Nullable |
| locationUpdatedAt | DateTime | Nullable |

### Contrainte unique CRITIQUE
```php
#[UniqueEntity(
    fields: ['user', 'event'],
    message: 'Vous etes deja inscrit a cet evenement'
)]
```
**IMPORTANT**: Toujours verifier avant INSERT!

### Relations
```
ToRegister ---> User (user, ManyToOne)
ToRegister ---> Event (event, ManyToOne, onDelete CASCADE)
```

### Statuts possibles
| Statut | Label | Description |
|--------|-------|-------------|
| `confirmed` | Confirme | Inscription validee (defaut) |
| `pending` | En attente | En attente de validation |
| `cancelled` | Annule | Inscription annulee |

### Methodes metier

```php
// Verification localisation partagee
hasLocation(): bool  // latitude && longitude != null

// Genere lien WhatsApp avec infos evenement
getWhatsappLocationLink(): ?string
// Format: https://wa.me/{phone}?text={message}

// Peut annuler l'inscription?
canBeCancelled(): bool
// false si: deja annule OU evenement pas upcoming
```

### Coordonnees GPS
- **precision**: decimal(10,7) = ~1cm de precision
- **latitude**: -90 a +90
- **longitude**: -180 a +180
- **locationUpdatedAt**: Timestamp de derniere MAJ

### WhatsApp Integration
```php
// Numero valide: +33612345678 ou 0612345678
// Regex: /^\+?[0-9]{10,15}$/

// Lien genere automatiquement
$registration->getWhatsappLocationLink();
// -> https://wa.me/33612345678?text=Bonjour...
```

## Workflow d'inscription

```
1. User clique "S'inscrire"
2. Verifier: event->canRegister()
3. Verifier: !event->isUserRegistered($user)
4. Creer ToRegister avec user + event
5. Persister et flush
```

### Code type pour inscription
```php
// Dans le Controller
if (!$event->canRegister()) {
    throw new BadRequestHttpException('Inscription impossible');
}

if ($event->isUserRegistered($user)) {
    throw new BadRequestHttpException('Deja inscrit');
}

$registration = new ToRegister();
$registration->setUser($user);
$registration->setEvent($event);
// status et registeredAt auto-set dans __construct()

$em->persist($registration);
$em->flush();
```

## Erreurs frequentes a eviter

1. **Oublier la verification d'unicite** - UniqueEntity en place mais verifier cote controller aussi
2. **Cascade delete mal compris** - Si event supprime, registrations supprimees auto
3. **WhatsApp sans validation** - Toujours valider le format du numero
4. **GPS precision** - Ne pas tronquer les decimales
