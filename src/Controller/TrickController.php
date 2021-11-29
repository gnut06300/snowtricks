<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Service\PictureService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick", name="trick")
     */
    public function index(): Response
    {
        dump($this->getUser());
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'StrickController',
        ]);
    }

    /**
     * @Route("/trick/new", name="trick_create")
     * @Route("/trick/{slug}/edit", name="trick_edit")
     */
    public function form(Trick $trick = null,Request $request, SluggerInterface $slugger, EntityManagerInterface $manager, PictureService $pictureService): Response
    {
        if(!$trick){
            $trick = new Trick();
            $this->denyAccessUnlessGranted('create',$trick);
        } else {
            $this->denyAccessUnlessGranted('edit',$trick);
        }
        // elseif (($trick->getAuthor() !== $this->getUser()) and !$this->isGranted('ROLE_ADMIN')) {
        //     throw $this->createAccessDeniedException();
        // }
        /* $form = $this->createFormBuilder($trick)
                    ->add('name')
                    ->add('description')
                    ->add('picture')
                    ->getForm();
        */
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if (!$trick->getId()) {
                $trick->setCreatedAt(new DateTime())
                    ->setAuthor($this->getUser());
                $this->addFlash('success', "Le Trick \"".$trick->getName()."\" à bien été créé");
           }
           else {
               $this->addFlash('success', "Le Trick à bien été modifié");
           }
            //we recover the transmitted images
            //dd($request->files->get('trick')['pictures'][0]->getClientOriginalName());
            //dd($form->get('pictures')->getData());
            $pictures = $form->get('pictures')->getData();

            //we loop on the pictures
            foreach($pictures as $picture){
                $pictureService->uploadPictures($picture,$trick);
            }

            $trick->setSlug(strtolower($slugger->slug($trick->getName(),'_')));
            $trick->setUpdatedAt(new DateTime());

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/create.html.twig', [
            'editMode' => $trick->getId() !== null,
            'trick' => $trick,
            'formTrick' => $form->createView()
        ]);
    }

    /**
     * @Route("/trick/{slug}/", name="trick_show")
     */
    public function showTricks(Trick $trick, CommentRepository $commentRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);

        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()){
            $comment->setUpdatedAt(new DateTime())
                    ->setAuthor($this->getUser())
                    ->setTrick($trick);

            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug(),'_fragment' =>'comments']);
            // return $this->redirect($this->generateUrl('trick_show', ['slug' => $trick->getSlug()]) .'#comments');
        }
        // $page = (int)$request->query->get("page", 1); provoque une erreur si ?page=toto ou 0
        if(!is_numeric($request->query->get("page", 1)) or (int)$request->query->get("page", 1) <= 0) {
            $page = 1 ;
        }  else {
            $page = (int)$request->query->get("page", 1);
        }

        $limit = 10 ;//we want 6 records per page

        $start = $limit * $page - $limit; //offset calculation (the start)
        $total = count($commentRepository->findBy(['trick' => $trick->getId()]));//Calculate the number of records

        $pages = ceil($total/$limit);// total page count rounded to the next whole number
        $comments=$commentRepository->findBy(['trick' => $trick->getId()],['createdAt' => 'DESC'],$limit,$start);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'page'=>$page,
            'pages'=>$pages,
            'comments'=>$comments,
            'formComment'=>$formComment->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="trick_delete", methods={"DELETE"}) // Voir , methods={"DELETE"}
     */
    public function delete(Request $request, Trick $trick, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('delete', $trick);
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            foreach ($trick->getPictures() as $image) 
            {
            //unlink($this->getParameter('images_directory'). '/' .$image->getFile());
            $pictureService->deletePicture($image->getFile());
            }
           
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        $this->addFlash('danger', "Le Trick \"" .$trick->getName()."\" à bien été supprimé");
        return $this->redirectToRoute('homepage');
    }

}
