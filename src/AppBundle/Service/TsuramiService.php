<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
use AppBundle\Repository\TsuramiRepository;
use AppBundle\Entity\Tsurami;
//use Symfony\Component\Cache\Adapter\TagAwareAdapter;
//use Symfony\Component\Cache\CacheItem;

class TsuramiService {
    /** @var EntityManager */
    private $entityManager;

    /** @var TsuramiRepository */
    private $tsuramiRepository;

//    /** @var TagAwareAdapter */
//    private $cache;

    /**
     * SecurityService constructor.
     * @param EntityManager $entityManager
     */
//    public function __construct(EntityManager $entityManager, TagAwareAdapter $cache = null) {
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
        $this->tsuramiRepository = $this->entityManager->getRepository('AppBundle:Tsurami');
//        $this->cache = $cache;
    }

    /**
     * @param string $userId
     * @return Tsurami[]
     */
    public function findTsuramisByUserId(string $userId, int $limit) {
//        $item = $this->getTsuramisCacheItemByUserId($userId);

//        if ($item->isHit()) {
//            return $item->get();
//        }

        /** @var Tsurami[] $tsuramis */
        $tsuramis = $this->tsuramiRepository->findByUserId($userId, $limit);
//        $item->set($tsuramis);
//        $this->cache->save($item);
        return $tsuramis;
    }

    /**
     * @return Tsurami[]
     */
    public function findRecentTsuramis(int $limit) {
        /** @var Tsurami[] $tsuramis */
        $tsuramis = $this->tsuramiRepository->findRecent($limit);
        return $tsuramis;
    }

    /**
     * @return Tsurami
     */
    public function findLatestTsurami() {
        /** @var Tsurami $tsurami */
        $tsurami = $this->tsuramiRepository->findLatest();
        return $tsurami;
    }

    /**
     * ツラミを作成して保存する。
     * @param $userId
     * @param $text
     */
    public function postTsurami($userId, $text) {
        $tsurami = $this->createTsurami($userId, $text);
        $this->save($tsurami);
//        $this->invalidateTsuramisCacheItemByUserId($userId);
    }

    /**
     * @param $userId
     * @param $text
     * @return Tsurami
     */
    public function createTsurami($userId, $text) {
        $tsurami = new Tsurami();
        $tsurami->setUserId($userId);
        $tsurami->setText($text);
        return $tsurami;
    }

    public function save(Tsurami $tsurami) {
        $this->entityManager->persist($tsurami);
        $this->entityManager->flush();
    }

//    /**
//     * @param $userId
//     * @return CacheItem
//     */
//    private function getTsuramisCacheItemByUserId($userId) {
//        return $this->cache->getItem($this->getTsuramisKeyByUserId($userId));
//    }
//
//    private function invalidateTsuramisCacheItemByUserId($userId) {
//        $this->cache->deleteItem($this->getTsuramisKeyByUserId($userId));
//    }
//
//    private function getTsuramisKeyByUserId($userId) {
//        return 'tsuramis_by_user_id_' . $userId;
//    }
}
