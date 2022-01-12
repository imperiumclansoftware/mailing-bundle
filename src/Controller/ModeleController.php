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

class ModeleController extends AbstractController
{

    /**
     * @Route("/modele",name="ics-mailing-modele-homepage")
     */
    public function index(EntityManagerInterface $em)
    {
        $modeles = $em->getRepository(MailModele::class)->findAll();

        return $this->render('@Mailing/modele/index.html.twig', [
            'modeles' => $modeles
        ]);
    }

    /**
     * @Route("/modele/add",name="ics-mailing-model-add")
     * @Route("/modele/edit/{model}",name="ics-mailing-model-edit")
     * @Route("/modele/useredit/{model}/{user}",name="ics-mailing-model-useredit")
     */
    public function edit(Request $request, EntityManagerInterface $em, Environment $twig, MailModele $model = null,$user=false)
    {
        if ($model == null) {
            $model = new MailModele();
        }
        $type=MailModeleType::FULL;
        if($user)
        {
            $type=MailModeleType::USER;
        }
        $form = $this->createForm(MailModeleType::class, $model,['formType' => $type]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            $model->updateVars($twig);

            $em->persist($model);
            $em->flush();

            $this->addFlash('success', sprintf('The mail modele <b>%s</b> was saved succesfully', $model->getTitle()));

            return $this->redirectToRoute('ics-mailing-modele-homepage');
        }

        return $this->render('@Mailing/modele/edit.html.twig', [
            'form' => $form->createView(),
            'model' => $model
        ]);
    }

    /**
     * @Route("/modele/delete/{model}",name="ics-mailing-model-delete")
     */
    public function delete(EntityManagerInterface $em, MailModele $model = null)
    {
        $em->remove($model);
        $em->flush();
        $this->addFlash('warning', sprintf('The mail modele <b>%s</b> was removed succesfully', $model->getTitle()));
        return $this->redirectToRoute('ics-mailing-modele-homepage');
    }

    /**
     * @Route("/modele/test/{model}",name="ics-mailing-modele-test")
     */
    public function test(MailingService $service, MailModele $model)
    {

        //$form = $this->createForm(TestMailModelType::class,$model);
        //$form = $this->createForm(UserMailModelType::class,$model);

        $service->sendMail(
            $model,
            [
                'username' => $this->getUser(),
                'bgColor' => '#a7c0ff'
            ],
            [
                $this->getUser()->getEmail()
            ],
        );

        return $this->redirectToRoute('ics-mailing-homepage');
        return $this->render('@Mailing/modele/test.html.twig',[
          //  'form' => $form->createView()
        ]);
    }
}
