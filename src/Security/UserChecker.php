<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->isVerified()) {
            $this->resendVerifEmail($user);
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Votre compte n\'a pas été vérifié. Un nouveau email de confirmation vous à été envoyé.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // if (!$user instanceof AppUser) {
        //     return;
        // }

        // // user account is expired, the user may be notified
        // if (!$user->isVerified()) {
        //     throw new AccountExpiredException('Votre compte n\'a pas été vérifier.');
        // }
    }

    public function resendVerifEmail(UserInterface $user)
    {
        return $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('gnut@gnut.eu', 'Gerald COL'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}