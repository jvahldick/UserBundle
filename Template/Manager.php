<?php

namespace JHV\Bundle\UserBundle\Template;

/**
 * Manager
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class Manager implements ManagerInterface
{
    
    protected $layout;
    protected $block;
    protected $templates;
    
    public function __construct($defaultLayout, $defaultBlock, array $templates)
    {
        $this->layout       = $defaultLayout;
        $this->block        = $defaultBlock;
        $this->templates    = $templates;
    }
    
    public function getLayout()
    {
        return $this->layout;
    }
    
    public function getBlock()
    {
        return $this->block;
    }

    public function getTemplates()
    {
        return $this->templates;
    }
    
    public function getTemplate($identifier)
    {
        if (false === isset($this->templates[$identifier])) {
            throw new \JHV\Bundle\UserBundle\Exception\TemplateNotFoundException(sprintf(
                'The template %s was not found. Valid template identifiers: %s',
                $identifier,
                array_keys($this->getTemplates())
            ));
        }
        
        return $this->templates[$identifier];
    }
    
}