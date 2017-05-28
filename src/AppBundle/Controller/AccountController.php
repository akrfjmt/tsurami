<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SensioLabs\Security\Exception\RuntimeException;
use AppBundle\Service\AccountService;


class AccountController extends Controller
{
    /**
     * @Route("/account/login", name="login")
     * @see Route
     */
    public function loginAction(Request $request)
    {
        return $this->render('account/login.html.twig', [
        ]);
    }

    /**
     * @Route("/account/logout", name="logout")
     * @see Route
     */
    public function logoutAction(Request $request)
    {
        /** @var AccountService $accountService */
        $accountService = $this->container->get('app.account_service');
        $accountService->logout();
        $request->getBaseUrl();

        $redirectPath = $request->query->get('redirect_path', '');
        return $this->redirect($request->getUriForPath($redirectPath));
    }

    /**
     * @Route("/account/setting", name="setting")
     * @see Route
     */
    public function settingAction(Request $request)
    {
        /** @var AccountService $accountService */
        $accountService = $this->container->get('app.account_service');

        $user = $accountService->getMyUser();
        if($user === null) {
            throw new RuntimeException('ログインしてください');
        }

        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('username', null);
            if ($username == null) {
                throw new RuntimeException('ユーザ名を入力してください');
            }

            if ($accountService->haveUserByUsername($username)) {
                // 既にユーザがいるので何もしない
            } else {
                $user->setUsername($username);
                $accountService->saveUser($user);
            }
        }

        return $this->render('account/setting.html.twig', [
            'user' => $user
        ]);
    }
}
