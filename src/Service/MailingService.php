<?php

namespace ICS\MailingBundle\Service;

use Twig\Environment;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpClient\HttpClient;
use ICS\MailingBundle\Entity\MailUserInterface;
use ICS\MailingBundle\Entity\MailModele;
use Doctrine\ORM\EntityManagerInterface;

class MailingService
{

    private $mailer;
    private $twig;
    private $kernel;
    private $doctrine;
    private $client;

    public function __construct(MailerInterface $mailer, Environment $twig, KernelInterface $kernel, EntityManagerInterface $doctrine,HttpClientInterface $client)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->kernel = $kernel;
        $this->doctrine = $doctrine;
        $this->client = $client;
    }

    function sendMail(MailModele $model, array $vars = [], $receivers = [], $attachements = [], $copyReceivers = [], $cacheCopyReceivers = [])
    {
        $email = (new Email())
            ->from(new Address($model->getSender(),$model->getSenderName()));

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

        $email->priority($model->getPriority())
            ->subject($model->getSubject());
        
        // Ajout des autres pièces jointes
        foreach($attachements as $attachement)
        {
            $email->attachFromPath($attachement,basename($attachement),mime_content_type($attachement));
        }

        $body = $this->twig->render($model->getTemplate()->getTwig(), $vars);
        // Ajout des images au pièces jointes
        $imgAttach = $this->getImagesSources($body);
        
        $imgNumber=1;
        foreach($imgAttach as $uri => $appointment )
        {
            $name='';
            if(substr($uri,0,4) == 'http')
            {
                // Télécgargement de l'image
                $newFile=$this->downloadImage($uri);
                $infos = pathinfo($newFile);
                $name='image'.$imgNumber.'.'.$infos['extension'];
                $email->embedFromPath($newFile,$name);
                $tempFiles[] = $newFile; 
            }
            else
            {
                $file=$this->kernel->getProjectDir().'/public/'.$uri;
                $infos = pathinfo($file);
                $name='image'.$imgNumber.'.'.$infos['extension'];
                $email->embedFromPath($file,$name);
            }
            
            $imgAttach[$uri]='cid:'.$name;
            $imgNumber++;
        }
        
        foreach($imgAttach as $uri => $appointment )
        {
            $body=str_replace($uri,$appointment,$body);
        }

        if ($vars['text'] != null && $vars['text'] != '') {
            $email->text($vars['text']);
        }

        
        
        dump($body);
        $email->html($body);
        $this->mailer->send($email);

        // Suppression de l'image temporaire
        foreach($tempFiles as $file)
        {
           unlink($file);
        }
    }

    function downloadImage(string $url)
    {
        $response=$this->client->request('GET',$url);
        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors du téléchargement de '.$url);
        }
        $tempPath=$this->kernel->getProjectDir().'/temp/mailAttachements';
        if(!file_exists($tempPath))
        {
            mkdir($tempPath,0777,true);
        }

        $filename=$tempPath.'/'.basename($url);
        // get the response content in chunks and save them in a file
        // response chunks implement Symfony\Contracts\HttpClient\ChunkInterface
        $fileHandler = fopen($filename, 'w');
        foreach ($this->client->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        return $filename;
    }

    function getImagesSources(string $body)
    {
        $imgSources=[];

        $regexp = '<img[^>]+src=(?:\"|\')\K(.[^">]+?)(?=\"|\')';
        preg_match_all("/$regexp/", $body, $matches, PREG_SET_ORDER);
        
        foreach($matches as $uriImages)
        {
            foreach($uriImages as $uri)
            {
                $imgSources[$uri] = $uri;
            }
        }

        return $imgSources;
    }

    function getMailUserClass()
    {
        $classes=[];
        foreach($this->doctrine->getMetadataFactory()->getAllMetadata() as $metadata)
        {
            if(in_array(MailUserInterface::class,class_implements($metadata->getName())))
            {
                $classes[]=$metadata->getName();
            }
        }

        return $classes;
    }
}
