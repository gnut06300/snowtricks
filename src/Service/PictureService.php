<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

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

}

