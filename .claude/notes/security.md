# Notes: Domaine Security

## Roles disponibles

| Role | Description | Acces |
|------|-------------|-------|
| `ROLE_USER` | Utilisateur standard | Automatique pour tous |
| `ROLE_ADMIN` | Administrateur | Dashboard admin complet |

### Hierarchie des roles
```yaml
# config/packages/security.yaml
role_hierarchy:
    ROLE_ADMIN: ROLE_USER
```

## Entity User - Securite

### Interfaces implementees
```php
class User implements UserInterface, PasswordAuthenticatedUserInterface
```

### Methodes de verification
```php
// Verification de role
$user->hasRole('ROLE_ADMIN'): bool

// Shortcut admin
$user->isAdmin(): bool  // hasRole('ROLE_ADMIN')

// Roles garantis
$user->getRoles(): array  // Inclut toujours ROLE_USER
```

### Gestion des roles
```php
// Ajouter un role
$user->addRole('ROLE_ADMIN');

// Retirer un role
$user->removeRole('ROLE_ADMIN');

// Ne JAMAIS faire:
$user->setRoles(['ROLE_ADMIN']); // Ecrase ROLE_USER!
```

## Access Control (security.yaml)

```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/login, roles: PUBLIC_ACCESS }
    - { path: ^/register, roles: PUBLIC_ACCESS }
    - { path: ^/, roles: ROLE_USER }
```

### Routes publiques
- `/login` - Page de connexion
- `/register` - Inscription
- `/event/` - Liste des evenements
- `/event/{id}` - Detail evenement
- `/health` - Health check

### Routes protegees ROLE_USER
- `/my-registrations` - Mes inscriptions
- Inscription aux evenements

### Routes protegees ROLE_ADMIN
- `/admin/` - Dashboard
- `/admin/events` - Gestion evenements
- `/admin/categories` - Gestion categories
- `/admin/users` - Gestion utilisateurs

## Authentification

### Configuration form_login
```yaml
form_login:
    login_path: app_login
    check_path: app_login
    default_target_path: /
    enable_csrf: true
```

### CSRF Protection
**CRITIQUE**: Toujours activer CSRF sur les formulaires!

```php
// Dans FormType - NE JAMAIS FAIRE:
'csrf_protection' => false  // INTERDIT!
```

### Password Hashing
```php
// TOUJOURS utiliser le hasher
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

$hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
$user->setPassword($hashedPassword);
```

**JAMAIS de mot de passe en clair!**

## Verification dans les Controllers

### Verification admin
```php
#[IsGranted('ROLE_ADMIN')]
public function adminDashboard(): Response
```

### Verification utilisateur connecte
```php
$this->denyAccessUnlessGranted('ROLE_USER');
// ou
if (!$this->getUser()) {
    throw $this->createAccessDeniedException();
}
```

### Verification proprietaire
```php
// Verifier que l'utilisateur est le createur de l'evenement
if ($event->getCreatedBy() !== $this->getUser()) {
    throw $this->createAccessDeniedException();
}
```

## Comptes de demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@thecrosslove.com | admin123 |
| User | john.doe@example.com | password123 |

## Erreurs frequentes a eviter

1. **Oublier CSRF** - Toujours activer sur les forms
2. **Password en clair** - Toujours hasher
3. **Oublier verification ownership** - Verifier createdBy
4. **PUBLIC_ACCESS mal utilise** - Uniquement pour routes vraiment publiques
5. **setRoles() sans ROLE_USER** - Utiliser addRole() plutot
