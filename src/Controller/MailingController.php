<?php

namespace ICS\MailingBundle\Controller;

use Twig\Node\Expression\NameExpression;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ICS\MailingBundle\Service\MailingService;

use ICS\MailingBundle\Form\Type\MailModeleType;
use ICS\MailingBundle\Entity\MailModele;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

class MailingController extends AbstractController
{
 /**
     * @Route("/",name="ics-mailing-homepage")
     */
    public function index(EntityManagerInterface $em)
    {
        $modeles = $em->getRepository(MailModele::class)->findAll();

        return $this->render('@Mailing/index.html.twig', [
            'modeles' => $modeles
        ]);
    }
    

    
}
