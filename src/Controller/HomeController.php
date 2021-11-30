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
     * @Route("/home2", name="homepage2")
     */
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        // $page = (int)$request->query->get("page", 1); provoque une erreur si ?page=toto ou 0
        if(!is_numeric($request->query->get("page", 1)) or (int)$request->query->get("page", 1) <= 0) {
            $page = 1 ;
        }  else {
            $page = (int)$request->query->get("page", 1);
        }

        $limit = 10 ;//we want 10 records per page

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

    /**
     * @Route("/", name="homepage")
     */
    public function home(): Response
    {
       return $this->render('home/index_1.html.twig', [
            'arrow' => true
        ]);
    }

    /**
     * @Route("/tricks", name="tricks_list")
     * @Route("/tricks/{page<\d+>}/{limit}-per-page", name="trick_page_with_limit", methods={"GET"})
     */
    public function tiricksList(TrickRepository $trickRepository, $page = 1, int $limit = 10): Response
    {
        $start = $limit * $page - $limit; //offset calculation (the start)
        $total = count($trickRepository->findAll()); //Calculate the number of records

        $pages = ceil($total / $limit); // total page count rounded to the next whole number
        $tricks = $trickRepository->findBy([], ['createdAt' => 'DESC'], $limit, $start);

        return $this->render('home/_tricksList.html.twig', [
            'tricks' => $tricks,
            'page' => $page,
            'pages' => $pages,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/onload", name="onload")
     */
    public function onLoad(Request $request)
    {
        $newLimit = (int)$request->query->get("limit",10);
        return $this->json(['code' => 200, 'message' => 'Ca marche bien', 'limit' => $newLimit], 200);
    }
}
