<?php
namespace ICS\MailingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="mailing")
 */
class MailModele
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
     * @ORM\Column(type="string", length=2048, nullable=false)
     * 
     * @var string
     */
    private $subject;
     /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * 
     * @var string
     */
    private $title;
     /**
     * @ORM\Column(type="text", nullable=false)
     * 
     * @var string
     */
    private $content;
    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @var string
     */
    private $signature;

    private $logo;
    /**
     * @ORM\ManyToOne(targetEntity=MailTemplate::class)
     *
     * @var MailTemplate
     */
    private $template;
     /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * 
     * @var string
     */
    private $sender;
    /**
     * @ORM\Column(type="json", nullable=false)
     * 
     * @var ArrayCollection
     */
    private $vars;


    public function __construct()
    {
        $this->vars = new ArrayCollection();
    }
    /**
     * Get the value of subject
     */ 
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @return  self
     */ 
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of signature
     */ 
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set the value of signature
     *
     * @return  self
     */ 
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get the value of logo
     */ 
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the value of logo
     *
     * @return  self
     */ 
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get the value of template
     */ 
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the value of template
     *
     * @return  self
     */ 
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the value of sender
     */ 
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set the value of sender
     *
     * @return  self
     */ 
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get the value of vars
     */ 
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Set the value of vars
     *
     * @return  self
     */ 
    public function setVars($vars)
    {
        $this->vars = $vars;

        return $this;
    }

    /**
     * Get the value of content
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */ 
    public function setContent($content)
    {
        $this->content = $content;

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

    public function addVar($name)
    {
        if(!$this->vars->contains($name))
        {
            $this->vars->add($name);
        }
    }

    public function removeVar($name)
    {
        if($this->vars->contains($name))
        {
            $this->vars->remove($name);
        }
    }

    public function clearVar()
    {
        $this->vars = new ArrayCollection();
    }
}