<?php
namespace ICS\MailingBundle\Entity;

use Twig\Node\Expression\NameExpression;
use Twig\Environment;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="mailing")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="string", length=50, nullable=false)
     * 
     * @var string
     */
    private $code="basic";
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
     * @ORM\ManyToOne(targetEntity=MailTemplate::class, cascade={"persist"})
     *
     * @var MailTemplate
     */
    private $template;
     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @var string
     */
    private $sender;
    /**
     * @ORM\Column(type="json", nullable=false)
     */
    private $vars;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority = Email::PRIORITY_NORMAL;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $replyTo;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $senderName = '';

    public function __construct()
    {
        $this->vars = [];
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
        if($vars == null)
        {
            $this->vars = [];
        }
        else
        {
            $this->vars = $vars;
        }
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
        if($this->vars == null)
        {
            $this->vars = [];
        }

        if(!in_array($name,$this->vars))
        {
            $this->vars[] = $name;
        }
    }

    public function removeVar($name)
    {
        foreach($this->vars as $key => $var)
        {
            if($var == $name)
            {
                unset($this->vars[$key]);
            }
        }
        
        
    }

    public function clearVars()
    {
        $this->vars =[];
        
    }

        
    public function updateVars(Environment $twig)
    {
        $internals = ['title','content','signature','logo'];
        $template = $twig->load($this->getTemplate()->getTwig())->getSourceContext();
        $nodes=$twig->parse($twig->tokenize($template));
        foreach($this->parseTwigNodes($nodes) as $var)
        {
            if(!in_array($var,$internals))
            {
                $this->addVar($var);
            }
        }
    }

    private function parseTwigNodes($nodes)
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

    public function renderVars(array $vars=[])
    {
        $finalVars = [];
        foreach($this->vars as $var)
        {
            $finalVars[$var]='';
        }

        $finalVars['title']=$this->getTitle();
        $finalVars['logo']=$this->getLogo();

        foreach($vars as $key => $var)
        {
            $finalVars[$key] = $var;
        }

        return $finalVars;
    }

    /**
     * Get the value of text
     */ 
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the value of text
     *
     * @return  self
     */ 
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the value of priority
     */ 
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set the value of priority
     *
     * @return  self
     */ 
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get the value of replyTo
     */ 
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * Set the value of replyTo
     *
     * @return  self
     */ 
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * Get the value of senderName
     */ 
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set the value of senderName
     *
     * @return  self
     */ 
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;

        return $this;
    }

    /**
     * Get the value of code
     *
     * @return  string
     */ 
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @param  string  $code
     *
     * @return  self
     */ 
    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }
}