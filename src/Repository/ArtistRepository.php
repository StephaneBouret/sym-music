<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    protected $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Artist::class);
        $this->paginator = $paginator;
    }

    public function findDistinctProgramDates(): array
    {
        $qb = $this->createQueryBuilder('a')
                ->select('DISTINCT DATE(a.programDateAt) as programDateAt')
                ->where('a.programDateAt IS NOT NULL')
                ->orderBy('programDateAt', 'ASC');

        return array_map(
            fn ($result) => new \DateTimeImmutable($result['programDateAt']),
            $qb->getQuery()->getArrayResult()
        );
    }

    /**
     * Récupère les artistes en lien avec une recherche
     *
     * @param SearchData $search
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
    {
        $query = $this->getSearchQuery($search)->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            20
        );
    }

    private function getSearchQuery(SearchData $search): QueryBuilder
    {
        $query = $this->createQueryBuilder('a');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('a.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->programDateAt)) {
            $date = $search->programDateAt;
            $query = $query
                ->andWhere('DATE(a.programDateAt) = DATE(:date)')
                ->setParameter('date', $date);
        }

        return $query->orderBy('a.programDateAt', 'ASC');
    }

    public function findNextTwoProgramDates()
    {
        return $this->createQueryBuilder('a')
                ->where('a.programDateAt >= :currentDate')
                ->setParameter('currentDate', new \DateTimeImmutable())
                ->orderBy('a.programDateAt', 'ASC')
                ->setMaxResults(2)
                ->getQuery()
                ->getResult();
    }

    //    /**
    //     * @return Artist[] Returns an array of Artist objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Artist
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
