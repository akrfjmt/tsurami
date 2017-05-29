<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\TsuramiService;

class TopController extends Controller
{
    /**
     * @Route("/", name="top")
     * @see Route
     */
    public function indexAction(Request $request)
    {
        /** @var TsuramiService $tsuramiService */
        $tsuramiService = $this->container->get('app.tsurami_service');
        $tsurami = $tsuramiService->findLatestTsurami();

        return $this->render('top/index.html.twig', [
            'tsurami' => $tsurami
        ]);
    }
}
