<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
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
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'StrickController',
        ]);
    }

    /**
     * @Route("/trick/new", name="trick_create")
     * @Route("/trick/{slug}/edit", name="trick_edit")
     */
    public function form(Trick $trick = null,Request $request, SluggerInterface $slugger, EntityManagerInterface $manager): Response
    {
        if(!$trick){
            $trick = new Trick();
        }

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
                $trick->setCreatedAt(new \DateTime());
           }
            $trick->setSlug(strtolower($slugger->slug($trick->getName(),'_')));

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/create.html.twig', [
            'editMode' => $trick->getId() !== null,
            'formTrick' => $form->createView()
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

    /**
     * @Route("/delete/{id}", name="trick_delete") // Voir , methods={"DELETE"}
     */
    public function delete(Request $request, Trick $trick): Response
    {
        dump($trick);
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('homepage');
    }
}
