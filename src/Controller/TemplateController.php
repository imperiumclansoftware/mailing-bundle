<?php

namespace ICS\MailingBundle\Controller;

use Twig\Node\Expression\NameExpression;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ICS\MailingBundle\Service\MailingService;

use ICS\MailingBundle\Form\Type\MailTemplateType;
use ICS\MailingBundle\Form\Type\MailModeleType;
use ICS\MailingBundle\Entity\MailTemplate;
use ICS\MailingBundle\Entity\MailModele;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

class TemplateController extends AbstractController
{

    /**
     * @Route("/template",name="ics-mailing-template-homepage")
     */
    public function index(EntityManagerInterface $em,Environment $twig)
    {
        $templates = $em->getRepository(MailTemplate::class)->findAll();

        foreach($templates as $template)
        {
            $template->updateVars($twig);
        }

        return $this->render('@Mailing/template/index.html.twig', [
            'templates' => $templates
        ]);
    }

    /**
     * @Route("/template/show/{template}",name="ics-mailing-template-show")
     */
    public function show(Request $request, Environment $twig, EntityManagerInterface $em, MailTemplate $template = null)
    {
        $template->updateVars($twig);
        return $this->render('@Mailing/template/show.html.twig', [
            'template' => $template
        ]);
    }

    /**
     * @Route("/template/preview/{template}",name="ics-mailing-template-preview")
     */
    public function preview(Request $request, Environment $twig, EntityManagerInterface $em, MailTemplate $template = null)
    {
        return $this->render($template->getTwig(),$request->request->all());
    }

    /**
     * @Route("/template/add",name="ics-mailing-template-add")
     * @Route("/template/edit/{template}",name="ics-mailing-template-edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, MailTemplate $template = null)
    {
        if ($template == null) {
            $template = new MailTemplate();
        }

        $form = $this->createForm(MailTemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $template = $form->getData();
            $em->persist($template);
            $em->flush();

            $this->addFlash('success', sprintf('The mail template <b>%s</b> was saved succesfully', $template->getName()));

            return $this->redirectToRoute('ics-mailing-template-homepage');
        }

        return $this->render('@Mailing/template/edit.html.twig', [
            'form' => $form->createView(),
            'template' => $template
        ]);
    }

    /**
     * @Route("/template/delete/{template}",name="ics-mailing-template-delete")
     */
    public function delete(EntityManagerInterface $em, MailTemplate $template = null)
    {
        if ($template != null) {
            $em->remove($template);
            $em->flush();
            $this->addFlash('warning', sprintf('The mail modele <b>%s</b> was removed succesfully', $template->getName()));
        } else {
            $this->addFlash('error', sprintf('The mail modele <b>%s</b> was not removed. an error occured'));
        }

        return $this->redirectToRoute('ics-mailing-template-homepage');
    }
}
