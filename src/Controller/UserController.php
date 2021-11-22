<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Service\PictureService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/account", name="account_index")
     */
    public function myAccount(): Response
    {
            return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

     /**
     * @Route("/account/profile/", name="account_profile")
     */
    public function profile(EntityManagerInterface $manager,Request $request, PictureService $pictureService) 
    {
        $user = $this->getUser();
        
        $formAccount = $this->createForm(AccountType::class,$user);
        
        $formAccount -> handleRequest($request);

        $picture = $user->getPicture();
        
        if($formAccount->isSubmitted() && $formAccount->isValid()){

            if($picture){
                $pictureService->deletePicture($picture);
            }

            $pictureService->uploadPicture($formAccount->get('picture')->getData(), $user);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success','Le profil : '.$user->getUserIdentifier().' a bien été modifié');
           
            return $this->redirectToRoute('account_index');
        
        }
        
        return $this->render('user/profile.html.twig', [
          'formAccount'=>$formAccount->createView(),
          'user'=>$user,
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
        if($user ===  $this->getUser()){
            return $this->redirectToRoute('account_index');
        }
        
        return $this->render('user/index.html.twig', [
            'user'=>$user,
        ]);
    }
}
