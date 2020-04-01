<?php


namespace Fragmency\Routing;

class Routes
{
    /**
     * @param string $path
     * @param callable|string $callable (function name or callable | string with "Controller @ method")
     * @return Route
     */
    public static function GET($path, $callable){return Router::newRoute('GET',$path,$callable);}

    /**
     * @param string $path
     * @param callable|string $callable (function name or callable | string with "Controller @ method")
     * @return Route
     */
    public static function PUT($path, $callable){return Router::newRoute('PUT',$path,$callable);}

    /**
     * @param string $path
     * @param callable|string $callable (function name or callable | string with "Controller @ method")
     * @return Route
     */
    public static function PATCH($path, $callable){return Router::newRoute('PATCH',$path,$callable);}

    /**
     * @param string $path
     * @param callable|string $callable (function name or callable | string with "Controller @ method")
     * @return Route
     */
    public static function DELETE($path, $callable){return Router::newRoute('DELETE',$path,$callable);}
}