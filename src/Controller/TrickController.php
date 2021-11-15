<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Service\PictureService;
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
                $trick->setCreatedAt(new \DateTime())
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
                // //we generate a new file name
                // $fichier = md5(uniqid()) . '.' . $picture->guessExtension();

                // //we copy the file to the upload folder
                // $picture->move(
                //     $this->getParameter('images_directory'),
                //     $fichier
                // );

                // //store the name of the image in the database
                // $img = new Picture();
                // $img->setFile($fichier);
                // $trick->addPicture($img);
                $pictureService->uploadPictures($picture,$trick);
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
