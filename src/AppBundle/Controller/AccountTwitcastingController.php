<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\TwitcastingService;
use AppBundle\Service\AccountService;

/**
 * Class AccountTwitcastingController
 * @package AppBundle\Controller
 */
class AccountTwitcastingController extends Controller
{
    /**
     * ツイキャスURLにリダイレクトする。
     * @Route("/account/twitcasting/login", name="account_twitcasting_login")
     * @see Route
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        /** @var TwitcastingService $twitcastingService */
        $twitcastingService = $this->container->get('app.twitcasting_service');

        $nextUri = $request->query->get('next_uri', '');
        $cookie = new Cookie('next_uri', $nextUri);

        $response = new RedirectResponse($twitcastingService->getGetAccessTokenStep1Uri());
        $response->headers->setCookie($cookie);
        return $response;
    }

    /**
     * @Route("/account/twitcasting/callback", name="account_twitcasting_callback")
     * @see Route
     *
     * @param Request $request
     * @return Response
     */
    public function callbackAction(Request $request)
    {
        /** @var string $code */
        $code = $request->query->get('code', '');
//        /** @var string $state CSRFトークン */
//        $state = $request->query->get('state', '');

        /** @var string $result */
        $result = $request->query->get('result', '');
        if ($result === 'denied') {
            return new Response(
                '<html><body>Login process is denied by twitcasting.</body></html>'
            );
        }

        /** @var TwitcastingService $twitcastingService */
        $twitcastingService = $this->container->get('app.twitcasting_service');

        /** @var string $redirectUri */
        $redirectUri = $this->generateUrl('account_twitcasting_callback', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var string $twitcastingAccessToken TwitCastingのアクセストークン */
        $twitcastingAccessToken = $twitcastingService->getAccessTokenStep2($code, $redirectUri);

        /** @var string $twitcastingUserId */
        $twitcastingUserId = $twitcastingService->verifyCredentialsAndExtractTwitcastingUserId($twitcastingAccessToken);

        /** @var AccountService $accountService */
        $accountService = $this->container->get('app.account_service');
        if ($accountService->haveUserByTwitcastingUserId($twitcastingUserId)) {
            return $this->signin($request, $twitcastingUserId, $twitcastingAccessToken);
        } else {
            return $this->signup($request, $twitcastingUserId, $twitcastingAccessToken);
        }
    }

    /**
     * @param Request $request
     * @param string $twitcastingUserId
     * @param string $twitcastingAccessToken
     * @return RedirectResponse|Response
     */
    private function signin(Request $request, string $twitcastingUserId, string $twitcastingAccessToken) {
        /** @var AccountService $accountService */
        $accountService = $this->container->get('app.account_service');
        // 指定したツイキャスユーザIDのユーザが存在する場合、ツイキャストークンを更新してログイン
        $accountService->signinWithTwitcasting($twitcastingUserId, $twitcastingAccessToken);

        // cookieからnext_uriを取得
        $nextUri = $request->cookies->get('next_uri', '');
        if ($nextUri == '') {
            $nextUri = $this->generateUrl('top', [], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $response = new RedirectResponse($nextUri);

        // next_uriを削除
        $response->headers->clearCookie('next_uri');

        return $response;
    }

    private function signup(Request $request, string $twitcastingUserId, string $twitcastingAccessToken) {
        /** @var AccountService $accountService */
        $accountService = $this->container->get('app.account_service');

        // 指定したツイキャスユーザIDのユーザが存在しない場合、新規にユーザを作成してログイン
        $accountService->signupWithTwitcasting($twitcastingUserId, $twitcastingAccessToken);

        return new RedirectResponse($this->generateUrl('setting'));
    }
}
