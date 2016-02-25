<?php

class Router {
    private $routes;
   
    public function __construct() {
        $routesPath = ROOT . '\config\routes.php';
        $this->routes = include($routesPath);
    }
    
    /**
     * Return REQUEST URI 
     * @return string
     */
    private function getURL(){
        if(!empty($_SERVER['REQUEST_URI'])){
            return trim($_SERVER['REQUEST_URI'],'/');
        }
    }
    
    public function run(){
        //Получить строку запроса
        $uri=$this->getURL();
        //поиск совподений с адресом который был прописан в браузере через регул выраж
        
        foreach ($this->routes as $uriPattern=>$path){
            
            if(preg_match("~$uriPattern~",$uri)){//если вырожение совпадает
                $internelRoute =  preg_replace("~$uriPattern~", $path, $uri);
                $segments=  explode('/', $internelRoute);
                $controllerName =  array_shift($segments).'Controller';
                $controllerName=  ucfirst($controllerName);
                $actionName = 'action' . ucfirst(array_shift($segments));
                $parameters = $segments;
                $controllerFile = ROOT . '/controllers/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    include_once ($controllerFile);
                }
                
                $controllerObject = new $controllerName ();

                $result = call_user_func_array(array($controllerObject, $actionName), $parameters);
                if ($result != null) {
                    break;
                }
                
            }
            
            
        }
    } 
    
}
