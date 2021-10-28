<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixtures extends Fixture
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i=1; $i <= 18 ; $i++) { 
            $trick = new Trick();

            $trick->setName("Nom du trick $i")
                ->setDescription("<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam tempor libero ipsum, vel scelerisque nisl aliquet id. Aenean efficitur vitae neque maximus convallis. Nulla facilisi. Nulla auctor eleifend ullamcorper. Praesent fringilla massa vitae mi suscipit varius. Suspendisse potenti. Maecenas sed quam id justo cursus scelerisque. Nullam vel justo scelerisque, efficitur turpis a, pellentesque dui. Pellentesque aliquam nec risus non congue. Donec ipsum eros, rhoncus non fringilla quis, vulputate ac dui. Phasellus non commodo felis.</p>

                <p>Donec bibendum lectus velit, sed volutpat risus mollis a. Donec vehicula lacus ligula, mollis ornare mi tristique vel. Morbi sit amet libero ut est pretium consectetur. Proin id facilisis purus. Etiam bibendum, nisi et lobortis convallis, metus velit maximus neque, sit amet tincidunt metus dolor vitae tellus. Fusce metus enim, pretium quis semper vel, pulvinar sed mi. Aenean fermentum faucibus nibh sit amet ullamcorper. Suspendisse potenti. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque ultrices orci nec tortor ultricies ultricies. Aliquam sollicitudin mattis felis, eget malesuada justo commodo quis. Phasellus et ante a neque egestas efficitur. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nam ac orci turpis. Nunc sed aliquet quam. Maecenas sed aliquam sem.</P>")
                ->setPicture("http://via.placeholder.com/1600x900")
                ->setCreatedAt(new \DateTime())
                ->setSlug(strtolower($this->slugger->slug($trick->getName(),'_')));

            $manager->persist($trick);

        }

        $manager->flush();
    }
}
