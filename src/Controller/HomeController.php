<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        // $page = (int)$request->query->get("page", 1); provoque une erreur si ?page=toto ou 0
        if(!is_numeric($request->query->get("page", 1)) or (int)$request->query->get("page", 1) <= 0) {
            $page = 1 ;
        }  else {
            $page = (int)$request->query->get("page", 1);
        }

        $limit = 6 ;//on veut  enregistrement par pages 6

        $start = $limit * $page - $limit; //calcul de l'offset(le début)
        $total = count($trickRepository->findAll());//Calcul le nombre d'enregistrement

        $pages = ceil($total/$limit);// nbrs de page total arondi à l'entier supérieur
        $tricks=$trickRepository->findBy([],[],$limit,$start);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'page'=>$page,
            'pages'=>$pages
        ]);
    }
    
}
