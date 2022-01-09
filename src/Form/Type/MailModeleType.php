<?php

namespace ICS\MailingBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Mime\Email;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('title', TextType::class, [
                'label' => 'Title'
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo',
                'required' => false
            ])
            ->add('template', EntityType::class, [
                'class' => MailTemplate::class,
                'label' => 'Template',
                'required' => true
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Message'
            ])
            ->add('signature', CKEditorType::class, [
                'label' => 'Signature'
            ])
            ->add('sender', EmailType::class, [
                'label' => 'Sender',
                'attr' => [
                    'placeholder' => 'user@example.com'
                ]
            ])
            ->add('replyTo', EmailType::class, [
                'label' => 'Reply To',
                'attr' => [
                    'placeholder' => 'user@example.com'
                ]
            ])
            ->add('priority', ChoiceType::class, [
                'label' => 'Priority',
                'choices' => [
                    'Normal' => Email::PRIORITY_NORMAL,
                    'High' => Email::PRIORITY_HIGH,
                    'Highest' => Email::PRIORITY_HIGHEST,
                    'Low' => Email::PRIORITY_LOW,
                    'Lowest' => Email::PRIORITY_LOWEST,

                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text format',
                'attr' => [
                    "rows" => "10"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MailModele::class,
        ]);
    }
}
