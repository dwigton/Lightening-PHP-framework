<?php
class Standard_Page_View extends Lightening_View{
    protected function init(){
        $this->setTemplateFile('app/templates/standard_page_template.php');
        $this->addCss('media/css/reset.css');
        $this->addCss('media/css/styles.css');
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
