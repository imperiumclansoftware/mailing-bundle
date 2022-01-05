<?php
namespace ICS\MailingBundle\Service;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class MailingService {

    private $mailer;
    private $twig;
    private $kernel;

    public function __construct(MailerInterface $mailer,Environment $twig,KernelInterface $kernel)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->kernel=$kernel;
    }

    function sendMail()
    {
        $email = (new Email())
            ->from(new Address('hello@example.com','System sender'))
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->attachFromPath($this->kernel->getProjectDir().'/public/medias/attachement_1.jpg','attachement_1')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html($this->twig->render('@Mailing/mail/mailBase.html.twig'));

        $this->mailer->send($email);
        
    }


}