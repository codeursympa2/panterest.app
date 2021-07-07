<?php

namespace App\Anrchi;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

/**
 * @author Anrichidine Abdallah <abdouanjouanais@gmail.com>
 */
class EmailSendA{
   
    public static function sendAnrchi(User $user,EmailVerifier $emailVerifier){
        return $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
        (new TemplatedEmail())
            ->from(new Address('codeur269@panterest.app.sn', 'Panterest'))
           // Variable d'environnement ->from(new Address($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']))
           /**
            * Variable du container
            */
            //->from(new Address($this->getParameter('app.mail_from_address'), $this->getParameter('app.mail_from_name')))
            ->to($user->getEmail())
            ->subject('Veillez confirmer votre adresse s\'il vous plaît.')
            ->htmlTemplate('registration/confirmation_email.html.twig')
    );
    }
}




?>