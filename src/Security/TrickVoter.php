<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Trick;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TrickVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    const CREATE = 'create';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
         
    }

    protected function supports(string $attribute, $subject)
    {
        if(!in_array($attribute, [self::EDIT,self::DELETE,self::CREATE])) {
            return false;
        }
        if(!$subject instanceof Trick) {
            return false;
        }
        return true;
    }
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($attribute === self::CREATE){
            return true;
        }

        if($this->security->isGranted('ROLE_ADMIN')){
            return true;
        }
        /**
         * @var Trick $trick
         */
        $trick = $subject;
        if($trick->getAuthor() === $user){
            return true;
        }

        return false;
    }
}