<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns an array of Event objects sorted
     */
    public function findAllSorted(string $sortBy = 'date'): array
    {
        $qb = $this->createQueryBuilder('e');

        switch ($sortBy) {
            case 'title':
                $qb->orderBy('e.title', 'ASC');
                break;
            case 'date':
            default:
                $qb->orderBy('e.dateStart', 'ASC');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Search events by title or description
     */
    public function search(string $search): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.title LIKE :search')
            ->orWhere('e.description LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('e.dateStart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events by category name
     */
    public function findByCategory(string $categoryName): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.category', 'c')
            ->where('c.name = :category')
            ->orWhere('c.slug = :category')
            ->setParameter('category', $categoryName)
            ->orderBy('e.dateStart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all unique categories from events
     */
    public function findAllCategories(): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT c.name')
            ->leftJoin('e.category', 'c')
            ->where('c.name IS NOT NULL')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}
