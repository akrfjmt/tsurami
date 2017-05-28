<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TopController extends Controller
{
    /**
     * @Route("/", name="top")
     * @see Route
     */
    public function indexAction(Request $request)
    {
        return $this->render('top/index.html.twig', []);
    }
}
