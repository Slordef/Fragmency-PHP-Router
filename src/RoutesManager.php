<?php


namespace Fragmency\Routing;



use Fragmency\Core\Application;

abstract class RoutesManager
{
    private $method;
    private $path;
    private $callable;
    private $controller;
    private $view;
    private $is_function;
    private $params = [];

    public function __construct($method, $path, $callable)
    {
        $this->method = $method;
        $this->path = $path;
        $this->callable = $callable;
        $this->is_function = $this->parseCallable();
    }

    private function parseCallable(){
        if(is_string($this->callable)){
            [$controller,$view] = explode('@',$this->callable);
            if(isset($controller) && isset($view)){
                $this->controller = $controller;
                $this->view = $view;
                return false;
            }
        }
        if(is_callable($this->callable)) return true;
    }

    public function __call($name, $arguments)
    {
        $call = '_'.$name;
        if(is_callable([$this,$call])) return call_user_func_array([$this,$call],$arguments);
    }

    private function _params(array $params){
        $this->params = array_merge($this->params,$params);
    }

    private function genPatternIndicator($type){
        switch ($type){
            case 'number': return '(\d+)';
            case 'string': return '(\w+)';
            default: return "";
        }
    }

    private function genPattern(){
        $search = array_map(function ($k){return ':'.$k;},array_keys($this->params));
        $replace = array_map(function ($t){return $this->genPatternIndicator($t);},array_values($this->params));
        $pattern = str_replace($search,$replace,$this->path);
        $identifiersPattern = "%(".join('|',array_map(function ($k){return "(\:$k)";},array_keys($this->params))).")%";
        preg_match_all($identifiersPattern,$this->path,$identifiers,PREG_OFFSET_CAPTURE);
        return ["%^".$pattern."$%i",$identifiers];
    }

    private function cleanIdentifiers($identifiers){
        $newIdentifiers = [];
        foreach ($identifiers as $k => $i){
            if(strlen($i[0])) $newIdentifiers[] = $i;
        }
        return $newIdentifiers;
    }

    private function _match($method,$path){
        if($this->path === '*') return false;
        if($this->method !== $method) return false;
        [$pattern,$identifiers] = $this->genPattern();
        if(preg_match($pattern,$path,$matches,PREG_OFFSET_CAPTURE)){
            $identifiers = $this->cleanIdentifiers($identifiers[0]);
            $identifiers = array_map(function ($i){return substr($i[0],1);},$identifiers);
            $matches = array_map(function ($i){return $i[0];},array_slice($matches,1));
            $values = array_combine($identifiers,$matches);
            Application::setRoutingParam($values);
            return true;
        }
    }

    private function _isFunction(){
        return !!$this->is_function;
    }

    private function _runFunction(){
        if($this->_isFunction()){
            $call = $this->callable;
            return $call();
        }
        return false;
    }

    private function _getController(){return $this->controller;}
    private function _getView(){return $this->view;}
    private function _getPath(){return $this->path;}
}