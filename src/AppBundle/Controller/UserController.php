<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SensioLabs\Security\Exception\RuntimeException;
use AppBundle\Service\AccountService;
use AppBundle\Service\TsuramiService;

class UserController extends Controller
{
    /**
     * @Route("/users/{username}", name="users")
     * @see Route
     */
    public function myAction(Request $request, $username)
    {
        /** @var AccountService $accountService */
        $accountService = $this->container->get('app.account_service');

        $user = $accountService->findUserByUsername($username);
        if($user === null) {
            throw new RuntimeException('不明なユーザです');
        }

        /** @var TsuramiService $tsuramiService */
        $tsuramiService = $this->container->get('app.tsurami_service');

        $tsuramis = $tsuramiService->findTsuramisByUserId($user->getId());

        return $this->render('users/index.html.twig', [
            'user' => $user,
            'tsuramis' => $tsuramis
        ]);
    }
}
