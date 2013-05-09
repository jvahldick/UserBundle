<?php

namespace JHV\Bundle\UserBundle\Template;

use JHV\Bundle\UserBundle\Template\ManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renderer
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class Renderer implements RendererInterface
{
    
    protected $twig;
    protected $manager;
    
    public function __construct(\Twig_Environment $twig, ManagerInterface $templateManager)
    {
        $this->twig     = $twig;
        $this->manager  = $templateManager;
    }
    
    public function getTemplateManager()
    {
        return $this->manager;
    }

    public function render($template, array $parameters = array())
    {        
        $baseLayout = $this->getTemplateManager()->getLayout();
        $block      = $this->getTemplateManager()->getBlock();
        $layout     = $this->twig->loadTemplate($this->getTemplateManager()->getTemplate($template));
        
        if (false === empty($baseLayout)) {
            $content = $this->twig->render($layout->getTemplateName(), array_merge(array(
                'base_template' => $baseLayout,
            ), $parameters));
        } else {
            $content = (!empty($block)) ? $layout->renderBlock($block, $parameters) : $layout;
        }
        
        return $content;
    }
    
    public function renderResponse($template, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }
        
        $response->setContent($this->render($template, $parameters));
        return $response;
    }
    
}