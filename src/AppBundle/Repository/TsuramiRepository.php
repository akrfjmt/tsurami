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
            ->useQueryCache(true);
//            ->useResultCache(true)
//            ->setResultCacheLifetime(5);
//            ->setCacheable(true)
//            ->setCacheRegion('region_tsuramis')
//            ->setLifetime(60);

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
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->setResultCacheLifetime(10);

        return $query->getResult();
    }

    /**
     * @return \AppBundle\Entity\Tsurami
     */
    public function findLatest() {
        $query = $this->createQueryBuilder('t')
            ->setMaxResults(1)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->useQueryCache(true);

        return $query->getOneOrNullResult();
    }

}
