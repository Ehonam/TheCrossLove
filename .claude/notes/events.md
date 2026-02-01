# Notes: Domaine Events

## Entity Event (src/Entity/Event.php)

### Champs principaux
| Champ | Type | Contraintes |
|-------|------|-------------|
| id | int | `id_event` (convention projet) |
| title | string(160) | NotBlank, min 5 chars |
| slug | string(255) | Unique, auto-generated |
| description | text | NotBlank, min 20 chars |
| image | string(255) | Nullable |
| dateStart | DateTime | NotBlank, > now |
| dateEnd | DateTime | NotBlank, > dateStart |
| address | string(160) | NotBlank |
| postalCode | string(10) | Regex validation |
| city | string(100) | NotBlank, min 2 chars |
| country | string(100) | NotBlank |
| organizer | string(160) | NotBlank, min 2 chars |
| maxParticipants | int | Nullable (null = illimite) |
| status | string(20) | Default 'active' |
| createdAt | DateTime | Auto-set |
| updatedAt | DateTime | Auto-set via PreUpdate |

### Relations
```
Event ---> User (createdBy, ManyToOne)
Event ---> Category (category, ManyToOne, nullable)
Event <--- ToRegister[] (registrations, OneToMany, cascade remove)
```

### Methodes metier essentielles

```php
// Verifie si l'evenement est a venir
isUpcoming(): bool  // dateStart > now

// Verifie si l'evenement est passe
isPast(): bool      // dateEnd < now

// Verifie si l'evenement est en cours
isOngoing(): bool   // dateStart <= now && dateEnd >= now

// Verifie si complet
isFull(): bool      // count(registrations) >= maxParticipants

// Places disponibles (null si illimite)
getAvailableSeats(): ?int

// Peut s'inscrire?
canRegister(): bool // isUpcoming() && !isFull()

// Utilisateur deja inscrit?
isUserRegistered(?User $user): bool
```

### Statuts calcules (getComputedStatus)
- `cancelled` - Annule manuellement
- `past` - Date de fin passee
- `ongoing` - En cours
- `full` - Complet
- `upcoming` - A venir

### Generation du slug
```php
// Utilise SluggerInterface
computeSlug(SluggerInterface $slugger): static
// Format: {slug-du-titre}-{id}
```

### Validation importantes
- **dateStart**: Doit etre dans le futur lors de la creation
- **dateEnd**: Doit etre apres dateStart
- **slug**: Unique en base

### Conventions de nommage
- ID: `id_event` (pas `id`)
- Timestamps: `created_at`, `updated_at`

## Repository (EventRepository)

### Methodes courantes
- `findUpcomingEvents()` - Evenements futurs tries par date
- `findByCategory(Category $category)` - Par categorie
- `findByUser(User $user)` - Crees par un utilisateur

## Erreurs frequentes a eviter

1. **Ne pas setter le slug manuellement** - Utiliser `computeSlug()`
2. **Oublier cascade remove sur registrations** - Deja configure
3. **Verifier maxParticipants null** - Signifie illimite, pas 0
4. **Dates sans timezone** - Toujours utiliser DateTime standard
