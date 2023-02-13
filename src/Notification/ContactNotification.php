<?php 

namespace App\Notification; 

use Twig\Environment;
use App\Entity\Contact;
use Swift_Mailer;

class ContactNotification {

    /*
     * @var \Swift_Mailer
     */
    private $mailer;

    /*
     * @var Environment
     */
    private $renderer;


    public function __construct(\Swift_Mailer $mailer, Environment $renderer){
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notify(Contact $contact){
        $message =(new \Swift_Message('Message'))
            ->setFrom($contact->getEmail())
            ->setTo('Zephir1999@live.fr')
            ->setReplyTo($contact->getEmail())
            ->setBody($this->renderer->render('email/contact_mail.html.twig', [
                'contact'=>$contact
            ]),'text/html');
        $this->mailer->send($message);
    }



}