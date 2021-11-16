<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Service\PictureService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class PictureController extends AbstractController
{
    /**
     * @Route("/picture", name="picture")
     */
    public function index(): Response
    {
        return $this->render('picture/index.html.twig', [
            'controller_name' => 'PictureController',
        ]);
    }

    /**
     *  @Route("/picture/delete/{id}", name="picture_delete", methods={"DELETE"})
     */
    public function deletePicture(Picture $picture, Request $request, PictureService $pictureService)
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$picture->getId(), $data['_token'])) {
            // On utilise le service pour supprimer l'image
            $pictureService->deletePicture($picture->getFile());
            // On supprime l'image de la bd
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();

            // On répond en Json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }

    }
}
