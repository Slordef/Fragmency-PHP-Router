<?php


namespace Fragmency\Routing;

use Fragmency\Core\Application;
use Fragmency\Core\Request;

class Router
{
    private static $routes = [];

    public static function newRoute($method,$path,$callable){
        $route = new Route($method,$path,$callable);
        self::$routes[] = $route;
        return $route;
    }

    private $app;

    public function __construct(Application $app){
        $this->app = $app;
        $file = $this->app->getConfigFolder()."/router.php";
        if(file_exists($file)) require_once $file;
    }

    public function getRoute(){
        $path = Request::getURI();
        $method = Request::getMethod();
        foreach (self::$routes as $route){
            if($route->match($method,$path)) return $route;
            if($route->getPath() === '*') $any = $route;
        }
        if(isset($any) && $any) return $any;
        return false;
    }
}