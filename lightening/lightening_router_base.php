<?php
class Lightening_Router_Base
{
    private $_controllerFile = 'lightening/lightening_error_controller.php';
    private $_controller = 'Lightening_Error_Controller';
    private $_method = 'notFound';
    private $_parameters = array();
    private $_uri;
    
    public function __construct($uri){
        $this->_uri = $paths = explode("/",trim($uri," /"));
        $this->initializeRoutes();
    }
    
    protected function addRoute($pattern, $controller_route, $controller_class_name, $function_name){
        
        $route_patterns     = explode("/",trim($pattern," /"));
        $uri                = $this->_uri;;
        
        if($route_patterns[count($route_patterns)-1] == '...'){
            if(count($uri) >= count($route_patterns) - 1){
                $parameters = array_slice($uri, count($route_patterns) - 1);
                $uri = array_slice($uri,0, count($route_patterns) - 1);
                if(count($parameters)==1 && $parameters[0]==''){$parameters = array();}
            }else{
                return; //exit without a match if the uri is too short to possibly match the pattern
            }
            array_pop($route_patterns);
        }
        //Parameters are now stripped off the uri and route_pattern
        
        //Return if the uri and patterns are differing lengths making a match impossible
        if(count($uri) != count($route_patterns)){return;}
        
        //Return if any of the sub-expressions fail to match the corrosponding bit in the uri
        foreach($route_patterns as $index => $pattern){
            if(!preg_match('/^'.$pattern.'$/', $uri[$index])){return;}
        }
        
        //If program reaches this point the uri matches the pattern. Set Variables.
        
        $index = count($uri);
        foreach(array_reverse($uri) as $token){ 
            // Matching is done in reverse to ensure that #10 is never over-written by #1
            // The pattern #1\U is changed to ucfirst and #1\L is changed to lowercase.
            
            $controller_route = preg_replace("/\#$index\Q\U\E/",ucfirst($token),$controller_route);
            $controller_route = preg_replace("/\#$index\Q\L\E/",strtolower($token),$controller_route);
            $controller_route = preg_replace("/\#$index/",$token,$controller_route);
            
            $controller_class_name = preg_replace("/\#$index\Q\U\E/",ucfirst($token),$controller_class_name);
            $controller_class_name = preg_replace("/\#$index\Q\L\E/",strtolower($token),$controller_class_name);
            $controller_class_name = preg_replace("/\#$index/",$token,$controller_class_name);
            
            $function_name = preg_replace("/\#$index\Q\U\E/",ucfirst($token),$function_name);
            $function_name = preg_replace("/\#$index\Q\L\E/",strtolower($token),$function_name);
            $function_name = preg_replace("/\#$index/",$token,$function_name);
            
            $index--;
        }
        
        if(file_exists($controller_route)){
            
            require_once $controller_route;
            
            if(class_exists($controller_class_name)){
                
                $ControllerReflectionClass = new ReflectionClass($controller_class_name); 

                if($ControllerReflectionClass->hasMethod($function_name)){
                    
                    $functionReflection = $ControllerReflectionClass->getMethod($function_name);
                    
                    if(count($parameters) >= $functionReflection->getNumberofRequiredParameters()
                            && count($parameters) <= $functionReflection->getNumberofParameters()){
                        
                        $this->_controllerFile = $controller_route;
                        $this->_controller = $controller_class_name;
                        $this->_method = $function_name;
                        $this->_parameters = $parameters;
                        $this->_uri = $uri;
                    }
                }
            }
        }
        return;
    }
        
    public function controllerFile(){ return $this->_controllerFile; }
    
    public function controller(){ return $this->_controller; }
    
    public function method(){ return $this->_method; }
    
    public function parameters(){ return $this->_parameters; }
        
    protected function initializeRoutes(){
        $error = "Classes that inherit from Lightening_Router_Base must implement an initializeRoutes() function";    
        throw new Exception($error);
    }
}
?>
