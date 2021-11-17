<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\Video;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TrickFixtures extends Fixture
{
    protected $slugger;
    protected $passwordEncoder;
    

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->slugger = $slugger;
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager): void
    {
        $gnut = new User();
        $gnut->setUsername('gnut')
            ->setEmail('gnut@gnut.eu')
            ->setPassword($this->passwordEncoder->hashPassword(
                $gnut,
                'password'))
            ->setRoles(array('ROLE_ADMIN'))
            ->setIsVerified(true)
            ->setSlug('gnut');

        $manager->persist($gnut);

        // $product = new Product();
        // $manager->persist($product);
        for ($u=1; $u <= 3 ; $u++) { 
            $user = new User();

            $user->setUsername("test$u")
            ->setEmail("test$u@gnut.eu")
            ->setPassword($this->passwordEncoder->hashPassword(
                $user,
                'password'))
            ->setIsVerified(true)
            ->setSlug($this->slugger->slug($user->getUserIdentifier())->lower());

            $manager->persist($user);


            for ($j=1; $j <= mt_rand(1,3) ; $j++) { 
                $category = new Category();
    
                $category->setTitle("Categorie $u-$j")
                        ->setSlug($this->slugger->slug($category->getTitle())->lower());
                
                $manager->persist($category);

                for ($i=1; $i <= mt_rand(2,6) ; $i++) { 
                    $trick = new Trick();
        
                    $trick->setName("Trick $u-$j-$i")
                        ->setDescription("<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam tempor libero ipsum, vel scelerisque nisl aliquet id. Aenean efficitur vitae neque maximus convallis. Nulla facilisi. Nulla auctor eleifend ullamcorper. Praesent fringilla massa vitae mi suscipit varius. Suspendisse potenti. Maecenas sed quam id justo cursus scelerisque. Nullam vel justo scelerisque, efficitur turpis a, pellentesque dui. Pellentesque aliquam nec risus non congue. Donec ipsum eros, rhoncus non fringilla quis, vulputate ac dui. Phasellus non commodo felis.</p>
        
                        <p>Donec bibendum lectus velit, sed volutpat risus mollis a. Donec vehicula lacus ligula, mollis ornare mi tristique vel. Morbi sit amet libero ut est pretium consectetur. Proin id facilisis purus. Etiam bibendum, nisi et lobortis convallis, metus velit maximus neque, sit amet tincidunt metus dolor vitae tellus. Fusce metus enim, pretium quis semper vel, pulvinar sed mi. Aenean fermentum faucibus nibh sit amet ullamcorper. Suspendisse potenti. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque ultrices orci nec tortor ultricies ultricies. Aliquam sollicitudin mattis felis, eget malesuada justo commodo quis. Phasellus et ante a neque egestas efficitur. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nam ac orci turpis. Nunc sed aliquet quam. Maecenas sed aliquam sem.</P>")
                        //->setPicture("http://via.placeholder.com/1600x900")
                        ->setCreatedAt(new DateTime())
                        ->setSlug(strtolower($this->slugger->slug($trick->getName(),'_')))
                        ->setCategory($category)
                        ->setAuthor($user);
        
                    $manager->persist($trick);

                    for ($p=1; $p <= mt_rand(1,3) ; $p++) { 
                        $picture = new Picture();

                        $picture->setFile("bb720067cfb87feee50a02b587479ac0.jpg")
                                ->setTrick($trick);
                        $manager->persist($picture);
                    }

                    for ($v=0; $v <= mt_rand(1,2) ; $v++) { 
                        $video = new Video();

                        $video->setUrl("https://www.youtube.com/embed/0Oez89EoE_c")
                            ->setTrick($trick);
                        $manager->persist($video);
                    }
        
                }
            }
        }


        $manager->flush();
    }
}
