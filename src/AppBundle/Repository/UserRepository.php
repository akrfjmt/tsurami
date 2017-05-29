<?php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * @inheritdoc
     * @see UserLoaderInterface::loadUserByUsername
     *
     * @param string $username
     * @return \AppBundle\Entity\User|null
     */
    public function loadUserByUsername($username) {
        return $this->findByUsername($username);
    }

    /**
     * @param string $username
     * @return \AppBundle\Entity\User|null
     */
    public function findByUsername($username) {
        $query = $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->useQueryCache(true);

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $id
     * @return \AppBundle\Entity\User|null
     */
    public function findById($id) {
        $query = $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->useQueryCache(true);

        return $query->getOneOrNullResult();
    }

    /**
     * 指定したusernameのユーザが存在するかどうかを返す
     * @param $username
     * @return bool 指定したツイキャスユーザIDのユーザが存在する場合true、存在しない場合false
     */
    public function haveWithUsername($username) {
        $query = $this->createQueryBuilder('u')
            ->select('count(u.username)')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->useQueryCache(true);

        return $query->getSingleScalarResult() > 0;
    }

    /**
     * @param string $twitcastingUserId
     * @return \AppBundle\Entity\User|null
     */
    public function findByTwitcastingUserId($twitcastingUserId) {
        $query = $this->createQueryBuilder('u')
            // ↓ u.twitcasting_user_idで指定するとアクセス修飾子がprivateなのでダメ。
            ->where('u.twitcastingUserId = :twitcastingUserId')
            ->setParameter('twitcastingUserId', $twitcastingUserId)
            ->getQuery()
            ->useQueryCache(true);

        return $query->getOneOrNullResult();
    }

    /**
     * 指定したツイキャスユーザIDのユーザが存在するかどうかを返す
     * @param $twitcastingUserId
     * @return bool 指定したツイキャスユーザIDのユーザが存在する場合true、存在しない場合false
     */
    public function haveWithTwitcastingUserId($twitcastingUserId) {
        $query = $this->createQueryBuilder('u')
            ->select('count(u.twitcastingUserId)')
            ->where('u.twitcastingUserId = :twitcastingUserId')
            ->setParameter('twitcastingUserId', $twitcastingUserId)
            ->getQuery()
            ->useQueryCache(true);

        return $query->getSingleScalarResult() > 0;
    }
}
