<?php

namespace Mycms;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use models\CategoryTable;
use models\IndexModel;

class App
{
    
    private $request;
    private $router;
    private $routes;
    private $requestContext;
    private $controller;
    private $arguments;
    private $basePath;
    
    public static $instance = null;


    public static function getInstance($basePath)
    {
        if(is_null(static::$instance)) {
            static::$instance = new static($basePath);
        }

        return static::$instance;
    }

    
    private function __construct($basePath) 
    {
        $this->basePath = $basePath;

        $this->setRequest();
        $this->setRequestContext();
        $this->setRouter();

        $this->routes = $this->router->getRouteCollection();

        if(!isset($_SESSION['all_categories']) || empty($_SESSION['all_categories'])){
            $_SESSION['all_categories'] = CategoryTable::categoriesFormat();
        }
        $model = new IndexModel();
        if(!isset($_SESSION['popularPosts']) || empty($_SESSION['popularPosts'])){
            $_SESSION['popularPosts'] = $model->popularPosts(6);
        }
        if(!isset($_SESSION['popularCategories']) || empty($_SESSION['popularCategories'])){
            $_SESSION['popularCategories'] = $model->getPopularCategories(5);
        }
        
        new \App\Controllers\SitemapController();

    }

    private function setRequest() 
    {
        $this->request = Request::createFromGlobals();
    }

    private function setRequestContext() 
    {
        $this->requestContext = new RequestContext();
        $this->requestContext->fromRequest($this->request);
    }

    public function getRequest() {
        return $this->request;
    }

    public function getRequestContextt() {
        return $this->requestContext;
    }

    private function setRouter() 
    {
        $fileLocator = new FileLocator(array(CONF));
        $requestContext = new RequestContext('/');
        $this->router = new Router(
            new YamlFileLoader($fileLocator),
            CONF.'routes.yaml',
            ['cache_dir' => CACHE ],
            $requestContext
            
            //array('cache_dir' => $this->basePath.'/storage/cache')
        );
    }

    public function getController() 
    {
        $controllerResolver = new HttpKernel\Controller\ControllerResolver();
        
        return $controllerResolver->getController($this->request);
    }

    public function getArguments()
    {
        $argumentResolver = new HttpKernel\Controller\ArgumentResolver();
        return $argumentResolver->getArguments($this->request, $this->controller);
    }


     public function run()
     {
        $matcher = new Routing\Matcher\UrlMatcher($this->routes, $this->requestContext);
        
        try {
            $match = $matcher->match($this->request->getPathInfo());
            
            $this->request->attributes->add($match);
        
            $this->controller = $this->getController();
            
            $this->arguments =  $this->getArguments($this->controller);
            
            $response = call_user_func_array($this->controller, $this->arguments);
            
        } catch (\Throwable $th) {
            require '404.php';
        }
     }
}
