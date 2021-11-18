<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/account", name="account_index")
     */
    public function myAccount(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/user/{slug}", name="user_show")
     */
    public function index(User $user=null)
    {
        if(!$user instanceof User) {
            $this->addFlash('danger', "Cette utilisateur n'existe pas.");
            return $this->redirectToRoute('homepage');
        }
        
        return $this->render('user/index.html.twig', [
            'user'=>$user,

        ]);
    }
}
