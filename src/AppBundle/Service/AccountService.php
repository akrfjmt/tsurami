<?php
namespace AppBundle\Service;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Repository\UserRepository;
use AppBundle\Entity\User;

/**
 * Class SecurityService
 * @package AppBundle\Service
 */
class AccountService {
    /** @var EntityManager */
    private $entityManager;
    /** @var TokenStorageInterface  */
    private $tokenStorage;
    /** @var string */
    private $providerKey;
    /** @var \AppBundle\Repository\UserRepository */
    private $userRepository;

    /**
     * SecurityService constructor.
     * @param EntityManager $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @param string $providerKey
     */
    public function __construct(EntityManager $entityManager, TokenStorageInterface $tokenStorage, string $providerKey) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->providerKey = $providerKey;
        /** @var UserRepository $userRepository */
        $this->userRepository = $this->entityManager->getRepository('AppBundle:User');
    }

    /**
     * @param string $id
     * @return User|null
     */
    public function findUserById(string $id) {
        /** @var User|null $user */
        $user = $this->userRepository->findById($id);
        return $user;
    }

    /**
     * @return User|null
     */
    public function getMyUser() {
        $userId = $this->tokenStorage->getToken()->getUser();
        return $this->findUserById($userId);
    }

    /**
     * @param string $username
     * @return bool
     */
    public function haveUserByUsername(string $username) {
        return $this->userRepository->haveWithUsername($username);
    }

    public function findUserByUsername(string $username) {
        return $this->userRepository->findByUsername($username);
    }

    /**
     * @param string $twitcastingUserId
     * @return bool
     */
    public function haveUserByTwitcastingUserId(string $twitcastingUserId) {
        return $this->userRepository->haveWithTwitcastingUserId($twitcastingUserId);
    }

    /**
     * @param string $twitcastingUserId
     * @return User|null
     */
    public function findUserByTwitcastingUserId(string $twitcastingUserId) {
        return $this->userRepository->findByTwitcastingUserId($twitcastingUserId);
    }

    /**
     * DBにユーザ情報を保存
     * @param User $user
     */
    public function saveUser(User $user) {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * ユーザからトークンを作成し、トークンストレージに積む
     * @param User $user
     */
    private function login(User $user) {
        $roles = $user->getRoles();
        $token = new UsernamePasswordToken($user->getId(), null, $this->providerKey, $roles);
        $this->tokenStorage->setToken($token);
    }

    /**
     * ログアウト処理を行う
     * ・トークンストレージを消す
     */
    public function logout() {
        $this->tokenStorage->setToken(null);
    }

    /**
     * ツイキャスユーザIDとツイキャスアクセストークンからユーザを作成し、ユーザでログインする。
     * @param string $twitcastingUserId ツイキャスユーザID
     * @param string $twitcastingAccessToken ツイキャスアクセストークン
     */
    public function signupWithTwitcasting(string $twitcastingUserId, string $twitcastingAccessToken) {
        $user = new User();
        $user->setIsActive(true);
        $user->setTwitcastingUserId($twitcastingUserId);
        $user->setTwitcastingToken($twitcastingAccessToken);

        $this->saveUser($user);
        $this->login($user);
    }

    /**
     * ユーザのツイキャスアクセストークンを更新し、ユーザでログインする。
     * @param string $twitcastingUserId ツイキャスユーザID
     * @param string $twitcastingAccessToken ツイキャスアクセストークン
     */
    public function signinWithTwitcasting(string $twitcastingUserId, string $twitcastingAccessToken) {
        $user = $this->findUserByTwitcastingUserId($twitcastingUserId);
        $user->setTwitcastingToken($twitcastingAccessToken);

        $this->saveUser($user);
        $this->login($user);
    }
}
