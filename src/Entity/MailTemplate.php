<?php
namespace ICS\MailingBundle\Entity;

use Twig\Node\Expression\NameExpression;
use Twig\Environment;
use Doctrine\ORM\Mapping as ORM;

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

    private $vars=[];

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

    public function updateVars(Environment $twig)
    {
        $template = $twig->load($this->getTwig())->getSourceContext();
        $nodes=$twig->parse($twig->tokenize($template));
        foreach($this->parseTwigNodes($nodes) as $var)
        {
            if(!in_array($var,$this->vars))
            {
                $this->vars[]=$var;
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
}