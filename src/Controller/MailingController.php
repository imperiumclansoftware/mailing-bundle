<?php
namespace ICS\MailingBundle\Controller;

use Twig\Node\Expression\NameExpression;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ICS\MailingBundle\Form\Type\MailModeleType;
use ICS\MailingBundle\Entity\MailModele;
use Doctrine\ORM\EntityManagerInterface;

class MailingController extends AbstractController
{

    /**
    * @Route("/",name="ics-mailing-homepage")
    */
    public function index()
    {
        return $this->render('@Mailing/index.html.twig',[
            
        ]);
    }

    function parseTwigNodes($nodes)
    {
        $vars=[];
        foreach($nodes as $node)
        {
            if($node instanceof NameExpression)
            {
                $vars[]=$node->getAttribute('name');   
            }
            else
            {
                $vars = array_merge($vars,$this->parseTwigNodes($node));
            }
        }
        return $vars;
    }

    /**
    * @Route("/add",name="ics-mailing-model-add")
    * @Route("/edit/{model}",name="ics-mailing-model-edit")
    */
    public function edit(Request $request,EntityManagerInterface $em, Environment $twig, MailModele $model=null)
    {
        if($model == null)
        {
            $model=new MailModele();
        }

        $form = $this->createForm(MailModeleType::class,$model);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $model = $form->getData();

            $vars = $model->getVars();
            $model->clearVars();
            
            // Ajout des variables utilisateur
            foreach($vars as $var)
            {
                $model->addVar($var);
            }
            // Ajout des variables du template
            $template = $twig->load($model->getTemplate()->getTwig())->getSourceContext();
            $nodes=$twig->parse($twig->tokenize($template));
            foreach($this->parseTwigNodes($nodes) as $var)
            {
                $model->addVar($var);
            }
            
            $em->persist($model);
            $em->flush();
            $this->addFlash('success',sprintf('The mail modele <b>{0}</b> was saved succesfully',$model->getTitle()));
        }

        return $this->render('@Mailing/edit.html.twig',[
            'form' => $form->createView()
        ]);
 
    }

}