<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StrickController extends AbstractController
{
    /**
     * @Route("/strick", name="strick")
     */
    public function index(): Response
    {
        return $this->render('strick/index.html.twig', [
            'controller_name' => 'StrickController',
        ]);
    }

    /**
     * @Route("/strick/mute", name="strick_show")
     */
    public function showStricks(): Response
    {
        return $this->render('strick/show.html.twig', [
            'controller_name' => 'StrickController',
        ]);
    }
}
