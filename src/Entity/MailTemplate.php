<?php
namespace ICS\MailingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="mailing")
 */
class MailTemplate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * 
     * @var string
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=2048, nullable=false)
     * 
     * @var string
     */
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

    /**
     * Get the value of id
     *
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  integer  $id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}