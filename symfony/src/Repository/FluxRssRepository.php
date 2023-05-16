<?php

namespace App\Repository;

use App\Entity\FluxRss;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FluxRss>
 *
 * @method FluxRss|null find($id, $lockMode = null, $lockVersion = null)
 * @method FluxRss|null findOneBy(array $criteria, array $orderBy = null)
 * @method FluxRss[]    findAll()
 * @method FluxRss[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FluxRssRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FluxRss::class);
    }

    public function save(FluxRss $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FluxRss $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function PaginatedFluxRsses(): Query
    {
      $qb = $this->createQueryBuilder("f")
            ->orderBy("f.createdAt","DESC");

      return $qb->getQuery();
    }
}
