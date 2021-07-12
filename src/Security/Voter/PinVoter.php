<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PinVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['MANAGE_PIN'])
            && $subject instanceof \App\Entity\Pin;
    }
    /**
     * Si supports retoune trrue c-a-d nous avons un des attributs  ['PIN_EDIT', 'PIN_CREATE','PIN_DELETE'] on vient a cette 
     * methode
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface ) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'MANAGE_PIN':
                return $user->isVerified() && $user==$subject->getUser();
          }

        return false;
    }
}
