<?php

namespace JHV\Bundle\UserBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouterLoader implements LoaderInterface
{

    private $loaded = false;
    
    protected $routes;
    protected $routeCollection;
    
    /**
     * Construtor.
     * Os routes são separados por managers, portanto a chave para o array
     * dos elementos de routes é o próprio nome do user manager.
     * 
     * @param array $routes
     */
    public function __construct(array $routes)
    {        
        $this->routes = $routes;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $this->routeCollection = $routeCollection = new RouteCollection();
        $this->processManagerRoutes();

        return $routeCollection;
    }

    public function supports($resource, $type = null)
    {
        return 'jhv_user_extra' === $type;
    }
    
    /**
     * Efetuar um loop dentro do parâmetro enviado por construtor, no qual
     * a chave é o nome do manager registrado enquanto o valor é outro array
     * no qual a chave tem o significado da chave do grupo enquanto o real valor
     * contém todas as rotas.
     * 
     * @return void
     */
    protected function processManagerRoutes()
    {
        foreach ($this->routes as $manager => $info) {
            $this->processGroupRoutes($manager, $info);
        }
    }
    
    /**
     * Executar processamento de array baseado em um grupo
     * 
     * @param string $manager
     * @param array $group
     */
    protected function processGroupRoutes($manager, array $group)
    {
        foreach ($group as $identifier => $routes) {
            $prefix = $routes['prefix'];
            unset($routes['prefix']);
            
            $this->addGroupRoutes($identifier, $manager, $routes, $prefix);
        }
    }
    
    /**
     * Adicionar um grupo de rotas a coleção de rotas registradas.
     * Este método irá percorrer um array de rotas adicionando-as individualmente,
     * com um padrão de:
     * jhv_user 
     *  + _identificador do grupo
     *  + _chave do roteamento
     *  + _identificador do gerenciador
     * 
     * 
     * @param string    $identifier     Identificador do grupo
     * @param string    $manager        Gerenciador de informações
     * @param array     $routes         Array de rotas a serem registradas
     * @param string    $prefix         Prefixo do roteamento
     */
    protected function addGroupRoutes($identifier, $manager, array $routes, $prefix = '')
    {
        foreach ($routes as $key => $routeItem) {
            $route = new Route($prefix . $routeItem['path'], array(
                '_controller'   => $routeItem['controller'],
                'manager'       => $manager,
            ));
            
            // Especificação de request method
            if (!empty($routeItem['methods'])) {
                $route->setMethods($routeItem['methods']);
            }
            
            $this->routeCollection->add(
                sprintf('jhv_user_%s_%s_%s', $identifier, $key, $manager),
                $route
            );
        }
    }

    public function getResolver()
    {}

    public function setResolver(LoaderResolverInterface $resolver)
    {}

}