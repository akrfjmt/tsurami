<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SensioLabs\Security\Exception\RuntimeException;
use AppBundle\Service\AccountService;
use AppBundle\Service\TsuramiService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TsuramiController extends Controller
{
    /**
     * @Route("/my", name="my")
     * @see Route
     */
    public function myAction(Request $request)
    {
        /** @var AccountService $accountService */
        $accountService = $this->container->get('app.account_service');

        $user = $accountService->getMyUser();
        if($user === null) {
            throw new RuntimeException('ログインしてください');
        }

        /** @var TsuramiService $tsuramiService */
        $tsuramiService = $this->container->get('app.tsurami_service');

        if ($request->getMethod() == 'POST') {
            $text = $request->request->get('tsurami', null);
            if ($text == null) {
                throw new RuntimeException('ツラミを入力してください');
            }

            $tsuramiService->postTsurami($user->getId(), $text);

            // リロード時の二重投稿対策
            return new RedirectResponse($this->generateUrl('my'));
        }

        $tsuramis = $tsuramiService->findTsuramisByUserId($user->getId());

        return $this->render('my/index.html.twig', [
            'user' => $user,
            'tsuramis' => $tsuramis
        ]);
    }

    /**
     * @Route("/recent", name="recent")
     * @see Route
     */
    public function recentAction(Request $request)
    {
        /** @var TsuramiService $tsuramiService */
        $tsuramiService = $this->container->get('app.tsurami_service');
        $tsuramis = $tsuramiService->findRecentTsuramis(1000);

        return $this->render('recent/index.html.twig', [
            'tsuramis' => $tsuramis
        ]);
    }
}
