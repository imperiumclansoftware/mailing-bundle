<?php

namespace ICS\MailingBundle\Service;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use ICS\MailingBundle\Entity\MailModele;

class MailingService
{

    private $mailer;
    private $twig;
    private $kernel;

    public function __construct(MailerInterface $mailer, Environment $twig, KernelInterface $kernel)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->kernel = $kernel;
    }

    function sendMail(MailModele $model, array $vars = [], $receivers = [], $copyReceivers = [], $cacheCopyReceivers = [])
    {
        $email = (new Email())
            ->from(new Address($model->getSender()));

        $vars = $model->renderVars($vars);

        $template = $this->twig->createTemplate($model->getContent());
        $vars['content'] = $template->render($vars);

        $template = $this->twig->createTemplate($model->getSignature());
        $vars['signature'] = $template->render($vars);

        $template = $this->twig->createTemplate($model->getText());
        $vars['text'] = $template->render($vars);

        foreach ($receivers as $key => $receiver) {
            if (is_string($key)) {
                $email->addTo(new Address($receiver, $key));
            } else {
                $email->addTo(new Address($receiver));
            }
        }

        foreach ($copyReceivers as $key => $receiver) {
            if (is_string($key)) {
                $email->addCc(new Address($receiver, $key));
            } else {
                $email->addCc(new Address($receiver));
            }
        }

        foreach ($cacheCopyReceivers as $key => $receiver) {
            if (is_string($key)) {
                $email->addBcc(new Address($receiver, $key));
            } else {
                $email->addBcc(new Address($receiver));
            }
        }

        if ($model->getReplyTo() != null) {
            $email->replyTo($model->getReplyTo());
        }

        if ($vars['text'] != null && $vars['text'] != '') {
            $email->text($vars['text']);
        }

        $email->priority($model->getPriority())
            ->subject($model->getSubject())
            ->html($this->twig->render($model->getTemplate()->getTwig(), $vars));

        //->attachFromPath($this->kernel->getProjectDir().'/public/medias/attachement_1.jpg','attachement_1')

        $this->mailer->send($email);
    }
}
