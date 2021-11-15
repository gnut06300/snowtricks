<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Picture;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService
{
    private ParameterBagInterface $params;
    
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function deletePicture(string $pictureFile)
    {
        $filesystem = new Filesystem();
        $picturePath = $this->params->get('images_directory'). '/' .$pictureFile;
        if($filesystem->exists($picturePath)){
            unlink($picturePath);  
        }
    }
    
    public function uploadPictures($pictureFile,Trick $trick)
    {
        //we generate a new file name
        $fichier = md5(uniqid()) . '.' . $pictureFile->guessExtension();

        //we copy the file to the upload folder
        $pictureFile->move(
            $this->params->get('images_directory'),
            $fichier
        );

        //store the name of the image in the database
        $img = new Picture();
        $img->setFile($fichier);
        $trick->addPicture($img);
    }
}

