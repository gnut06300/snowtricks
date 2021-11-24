<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("/comment/{id}/edit", name="comment_edit")
     */
    public function editComment(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('edit',$comment);
        $trick = $comment->getTrick();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);
        if($formComment->isSubmitted() && $formComment->isValid()){
            $comment->setUpdatedAt(new DateTime());
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }
        return $this->render('comment/index.html.twig', [
            'formComment'=>$formComment->createView(),
        ]);
    }

    /**
     * @Route("comment/delete/{id}", name="comment_delete", methods={"DELETE"}) // Voir , methods={"DELETE"}
     */
    public function delete(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted('delete', $comment);
        $trick = $comment->getTrick();
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        $this->addFlash('danger', "Le commentaire à bien été supprimé");
        return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
    }
}
