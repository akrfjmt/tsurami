<?php

namespace BaseBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApcController extends Controller
{
    /**
     * @Route("/apc", name="apc")
     * @see Route
     */
    public function indexAction(Request $request)
    {
        ob_start();
        include 'apc.php';
        $result = ob_get_clean();
        return new Response($result);
    }
}
