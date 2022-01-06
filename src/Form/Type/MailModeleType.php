<?php
namespace ICS\MailingBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use ICS\MailingBundle\Entity\MailerReceiver;
use ICS\MailingBundle\Entity\MailTemplate;
use ICS\MailingBundle\Entity\MailModele;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class MailModeleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class)
            ->add('title', TextType::class,[
                'label' => 'Title'
            ])
            ->add('logo', FileType::class,[
                'label' => 'Logo',
                'required' => false
            ])
            ->add('template', EntityType::class,[
                'class' => MailTemplate::class,
                'label' => 'Template',
                'required' => true
            ])
            ->add('content', CKEditorType::class,[
                'label' => 'Message'
            ])
            ->add('signature', CKEditorType::class,[
                'label' => 'Signature'
            ])
            ->add('sender', EmailType::class,[
                'label' => 'Sender',
                'attr' => [
                    'placeholder' => 'user@example.com'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MailModele::class,
        ]);
    }
}