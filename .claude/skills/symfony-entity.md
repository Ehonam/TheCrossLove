# Skill: Symfony Entity

Cree une entite avec les conventions TheCrossLove.

## Conventions du projet

### Nommage ID
```php
#[ORM\Column(name: 'id_<entity>')]
private ?int $id = null;
```

### Timestamps
```php
#[ORM\Column(type: Types::DATETIME_MUTABLE)]
private ?\DateTimeInterface $createdAt = null;

#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
private ?\DateTimeInterface $updatedAt = null;
```

### Lifecycle callbacks
```php
#[ORM\HasLifecycleCallbacks]
class MyEntity
{
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
```

## Template d'entite

```php
<?php

namespace App\Entity;

use App\Repository\{EntityName}Repository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: {EntityName}Repository::class)]
#[ORM\HasLifecycleCallbacks]
class {EntityName}
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_{entity_lower}')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    // Getters/Setters...
}
```

## Fichiers a creer

1. **Entity**: `src/Entity/{EntityName}.php`
2. **Repository**: `src/Repository/{EntityName}Repository.php`
3. **Test**: `tests/Unit/Entity/{EntityName}Test.php`

## Template Repository

```php
<?php

namespace App\Repository;

use App\Entity\{EntityName};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class {EntityName}Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, {EntityName}::class);
    }
}
```

## Template Test

```php
<?php

namespace App\Tests\Unit\Entity;

use App\Entity\{EntityName};
use PHPUnit\Framework\TestCase;

class {EntityName}Test extends TestCase
{
    public function testConstruction(): void
    {
        $entity = new {EntityName}();

        $this->assertInstanceOf(\DateTimeInterface::class, $entity->getCreatedAt());
    }
}
```

## Workflow

1. Creer l'Entity avec le template
2. Creer le Repository
3. Creer le test unitaire
4. Generer la migration: `php bin/console make:migration`
5. Executer les tests: `php bin/phpunit tests/Unit/Entity/`
