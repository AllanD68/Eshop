<?php

namespace App\Notification;

// On importe les classes nécessaires à l'envoi d'e-mail et à twig

use App\Entity\User;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ActivationAccountNotification
{
    /**
     * Propriété contenant le module d'envoi de mails
     * 
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * Propriété contenant l'environnement Twig
     *
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * Méthode de notification (envoi de mail)
     *
     * @return void
     */
    public function notify(User $user)
    {
        // On construit le mail
        $message = (new Swift_Message('Mon blog - Activation de votre compte'))
            // Expéditeur
            ->setFrom('testsymfony5@gmail.com')
            // Destinataire
            ->setTo($user->getEmail())
            // Corps du message
            ->setBody(
                $this->renderer->render(
                    'email/activation.html.twig',
                    ['token' => $user->getActivationToken()]
                ),
                'text/html'
            );

        // On envoie le mail
        $this->mailer->send($message);
        
    }
}