<?php

class IndexController extends Zend_Controller_Action{

    public function init(){
        $this->view->headScript()->appendFile('/js/front/index.js?'.time());
       
    }//function
 
    public function indexAction(){
      
    }//function
 
    public function aboutAction(){
      
    }//function

    public function contactAction(){
      
    }//function

    public function loginAction()
    {
    	
    }



    public function preRegisterAction()
    {
      $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
      $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' WHERE status = 1 ', ' estado asc');
      $this->view->tipo_Pregistro = My_Comun::obtenerFiltroSQL('tipo_preregistro', ' WHERE status = 1 ', ' descripcion asc');

    }


    public function guardarPreRegistroAction()
    {
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(TRUE);
      unset($_POST['correo2']);

      $preregistroID = My_Comun::guardarSQL("pre_registro", $_POST, $_POST["id"], "");
      echo $preregistroID;    
    }

  



}

