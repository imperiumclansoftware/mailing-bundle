<?php
namespace ICS\MailingBundle\Interfaces;

interface MailerReceiverInterface
{

    public function getMail();

    public function getName();

    public function getSurName();

}