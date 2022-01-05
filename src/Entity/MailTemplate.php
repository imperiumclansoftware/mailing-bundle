<?php
namespace ICS\MailingBundle\Entity;

class MailTemplate
{
    private $name;

    private $twig;

    /**
     * Get the value of twig
     */ 
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * Set the value of twig
     *
     * @return  self
     */ 
    public function setTwig($twig)
    {
        $this->twig = $twig;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}