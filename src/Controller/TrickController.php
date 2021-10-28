<?php

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick", name="strick")
     */
    public function index(): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'StrickController',
        ]);
    }

    /**
     * @Route("/trick/{slug}", name="trick_show")
     */
    public function showTricks(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'controller_name' => 'StrickController',
            'trick' => $trick
        ]);
    }
}
