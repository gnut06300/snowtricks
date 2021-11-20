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

        $limit = 10 ;//we want 6 records per page

        $start = $limit * $page - $limit; //offset calculation (the start)
        $total = count($trickRepository->findAll());//Calculate the number of records

        $pages = ceil($total/$limit);// total page count rounded to the next whole number
        $tricks=$trickRepository->findBy([],['createdAt' => 'DESC'],$limit,$start);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'page'=>$page,
            'pages'=>$pages,
            'arrow'=>true
        ]);
    }
    
}
