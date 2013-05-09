<?php

namespace JHV\Bundle\UserBundle\Template;

use Symfony\Component\HttpFoundation\Response;

/**
 * RendererInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface RendererInterface
{
    
    /**
     * Localizar a engine dos templates.
     * Basicamente é a extensão dos templates registrados.
     * 
     * @return string Engine de extensão para os templates
     */
    function getTemplateManager();
    
    /**
     * Efetuará a renderização do template.
     * Localizará o layout principal passando por parâmetro base_template, 
     * fazendo desta forma um "extends" no layout desta variável.
     * 
     * Caso o layout principal não esteja definido, o método irá verificar
     * a existência de especificação de bloco, que caso esteja definido
     * irá renderizar este bloco, caso contrário renderizará o próprio
     * passado como parâmetro.
     * 
     * @param string    $template
     * @param array     $parameters
     * 
     * @return string Template renderizado
     */
    function render($template, array $parameters = array());
    
    /**
     * Gerar uma resposta do conteúdo passado através do template definido.
     * O método irá verificar a existência de um objeto Response, caso não
     * exista irá criá-lo, definindo o conteúdo baseado no template.
     * 
     * @param string $template
     * @param array $parameters
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    function renderResponse($template, array $parameters = array(), Response $response = null);
    
}