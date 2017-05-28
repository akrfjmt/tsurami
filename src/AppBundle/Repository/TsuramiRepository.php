<?php
namespace AppBundle\Repository;

use Doctrine\ORM\Cache;
use Doctrine\ORM\EntityRepository;

class TsuramiRepository extends EntityRepository
{
    /**
     * @param string $userId
     * @param int $limit
     * @return \AppBundle\Entity\Tsurami[]
     */
    public function findByUserId(string $userId, int $limit) {
        $query = $this->createQueryBuilder('t')
            ->where('t.userId = :userId')
            ->setParameter('userId', $userId)
            ->setMaxResults($limit)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->setCacheable(true)
            ->setCacheRegion('region_tsuramis_by_id')
            ->setLifetime(60);
        ;

        return $query->getResult();
    }

    /**
     * @param int $limit
     * @return \AppBundle\Entity\Tsurami[]
     */
    public function findRecent(int $limit) {
        $query = $this->createQueryBuilder('t')
            ->setMaxResults($limit)
            ->orderBy('t.id', 'DESC')
            ->getQuery();

        $query->useQueryCache(true);
        $query->useResultCache(true);

        return $query->getResult();
    }

}
