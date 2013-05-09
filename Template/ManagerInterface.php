<?php

namespace JHV\Bundle\UserBundle\Template;

/**
 * ManagerInterface
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
interface ManagerInterface
{
    
    /**
     * Localizar o layout base do gerenciador em questão.
     * 
     * @return string
     */
    function getLayout();
    
    /**
     * Localizar um bloco padrão para exibição do conteúdo.
     * 
     * @return string
     */
    function getBlock();
    
    /**
     * Localizar array dos templates registrados.
     * 
     * @return array
     */
    function getTemplates();
    
    /**
     * Localizar um template através de seu identificador.
     * 
     * @param string $identifier
     * @return string
     */
    function getTemplate($identifier);
    
}