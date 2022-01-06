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
    public function index(EntityManagerInterface $em)
    {
        $modeles = $em->getRepository(MailModele::class)->findAll();
        
        return $this->render('@Mailing/index.html.twig',[
            'modeles' => $modeles
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

        dump($model);
        $form = $this->createForm(MailModeleType::class,$model);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            
            $model = $form->getData();
            $em->persist($model);
            $em->flush();

            $model->clearVars();
            // Ajout des variables du template
            $template = $twig->load($model->getTemplate()->getTwig())->getSourceContext();
            $nodes=$twig->parse($twig->tokenize($template));
            foreach($this->parseTwigNodes($nodes) as $var)
            {
                $model->addVar($var);
            }
            
            $em->persist($model);
            $em->flush();
            dump($model);
            $this->addFlash('success',sprintf('The mail modele <b>%s</b> was saved succesfully',$model->getTitle()));
            // return $this->redirectToRoute('ics-mailing-homepage');
        }

        return $this->render('@Mailing/edit.html.twig',[
            'form' => $form->createView()
        ]);
 
    }

    /**
    * @Route("/delete/{model}",name="ics-mailing-model-delete")
    */
    public function delete(EntityManagerInterface $em,MailModele $model=null)
    {
        $em->remove($model);
        $em->flush();
        $this->addFlash('warning',sprintf('The mail modele <b>%s</b> was removed succesfully',$model->getTitle()));
        return $this->redirectToRoute('ics-mailing-homepage');
    }
}