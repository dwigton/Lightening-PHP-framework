<?php
class Lightening_View{

    private $_child_views;
    private $_css_files = array();
    private $_script_files = array();
    private $_var = array();
    private $_template_file_path;
    
    public function __construct($template_file_path){
        $this->_template_file_path = $template_file_path;
        $this->init();
    }
    
    protected function init(){
        //Should be overriden in inherited classes.
    }
    
    public static function newExtendedView($view_file_path, $view_class_name, $template_file_path = null){
        require_once $view_file_path;
        return new $view_class_name($template_file_path);
    }
    
    public function addChild($handle, $child_view){ 
        $this->_child_views[$handle] = $child_view;
        return $this;
    }
    
    public function addNewChild($handle, $template_file_path){
        $this->_child_views[$handle] = new Lightening_View($template_file_path);
        return $this;
    }
    
    public function addNewExtendedChild($handle, $view_file_path, $view_class_name){
        require_once $view_file_path;
        $this->_child_views[$handle] = new $view_class_name($view_file_path);
        return $this;
    }
    
    public function getChild($handle){ return $this->_child_views[$handle]; }
     
    public function setVar($handle, $value){ 
        $this->_var[$handle]=$value;
        return $this;
    }
    
    public function getVar($handle){ return $this->_var[$handle]; }
    
    public function setTemplateFile($template_file_path){
        $this->_template_file_path = $template_file_path;
        return $this;
    }
    
    public function render($variable_array = false){
        if(is_array($variable_array)){
            $this->_var = array_merge($this->_var, $variable_array);
        }
        extract($this->_var);
        require $this->_template_file_path;
        return $this;
    }
    
    protected function renderChild($handle){ 
        $this->_child_views[$handle]->render($this->_var);
        return $this;
    }
    
    public function addCss($css_file){
        $this->_css_files[$css_file] = $css_file;
        return $this;
    }
    
    public function addScript($script_file_name){
        $this->_script_files[$script_file_name] = $script_file_name;
        return $this;
    }
    
    protected function getCss(){
        $output = "";
        $cssArray = $this->_css_files;
        foreach ($this->_child_views as $child_view){
            $cssArray = array_merge($cssArray,$child_view->cssArray());
        }
        
        foreach($cssArray as $css_file){
            $output .= "<link rel='stylesheet' type='text/css' href='$css_file' />";
        }
        
        return $output;
    }
    
    protected function getScript(){
        $output = "";
         $scriptArray = $this->_script_files;
        foreach ($this->_child_views as $child_view){
            $scriptArray = array_merge($scriptArray,$child_view->scriptArray());
        }
        
        foreach($scriptArray as $script_file){
            $output .= "<script type='text/javaScript' src='$script_file' ></script>";
        }
        
        return $output;
    }
    
    public function cssArray(){ return $this->_css_files; }
    
    public function scriptArray(){ return $this->_script_files; }
}
?>
