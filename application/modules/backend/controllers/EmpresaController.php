<?php
class Backend_EmpresaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/empresa.js');
       
    }//function
 
    public function indexAction(){
    	$sess=new Zend_Session_Namespace('permisos');
    	$this->view->puedeAgregar=strpos($sess->cliente->permisos,"AGREGAR_EMPRESA")!==false;

    }//function

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $nombre=$this->_getParam('nombre');
        $status=$this->_getParam('status');
        
        
        if($this->_getParam('status')!="")
            $filtro.=" AND status=".$this->_getParam('status');
        
        if($nombre!='')
        {
            $nombre=explode(" ", trim($nombre));
            for($i=0; $i<=$nombre[$i]; $i++)
            {
                $nombre[$i]=trim(str_replace(array("'","\"",),array("�","�"),$nombre[$i]));
        		if($nombre[$i]!="")
                    $filtro.=" AND (e.nombre LIKE '%".$nombre[$i]."%') ";
            }//for
        }//if

        $consulta = "SELECT e.*
                      FROM empresa e
                      WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        $editar = My_Comun::tienePermiso("EDITAR_EMPRESA");
    	$eliminar = My_Comun::tienePermiso("ELIMINAR_EMPRESA");
            
        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                $estado = My_Comun::obtenerSQL('estados','id_estado',$registros['registros'][$k]->estado_id);
                $municipio = My_Comun::obtenerSQL('municipios','id_municipio',$registros['registros'][$k]->municipio_id);
                $grid[$i]['nombre'] =$registros['registros'][$k]->nombre;
                $grid[$i]['razon_social'] =$registros['registros'][$k]->razon_social;
                $grid[$i]['domicilio'] =$registros['registros'][$k]->calle.' Num. Exterior: '.$registros['registros'][$k]->numExterior.' Num. Interior: '.$registros['registros'][$k]->numInterior;
                $grid[$i]['contacto'] =$registros['registros'][$k]->contacto;
                $grid[$i]['telefono'] =$registros['registros'][$k]->telefono;
                $grid[$i]['email'] =$registros['registros'][$k]->email;
                $grid[$i]['rfc'] =$registros['registros'][$k]->rfc;
                $grid[$i]['estado'] =$estado->estado;
                $grid[$i]['municipio'] =$municipio->nombre_municipio;
                $grid[$i]['status']=(($registros['registros'][$k]->status)?'Habilitado':'Inhabilitado');
               
            if($registros['registros'][$k]->status == 0)
            {
                $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';
                
                if($eliminar)
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Eliminar"><i class="boton fa fa-times-circle fa-lg azul"></i></span>';
                else
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle text-danger fa-lg "></i>';
            }
            else
            {
                    
                if($editar){
                    $grid[$i]['editar'] = '<span onclick="agregar(\'/backend/empresa/agregar\','.$registros['registros'][$k]->id.', \'frmEmpresa\',\'Edición de empresa\' );" title="Editar"><i class="boton fa fa-pencil fa-lg azul"></i></span>';
                }
                else{
                    $grid[$i]['editar'] = '<i class="boton fa fa-pencil fa-lg text-danger"></i>';
                }

                if($eliminar){
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->id.','.$registros['registros'][$k]->status.');" title="Deshabilitar / Habilitar"><i class="boton fa fa-times-circle fa-lg azul"></i></i></span>';
                }
                else{
                    $grid[$i]['eliminar'] = '<i class="boton fa fa-times-circle fa-lg text-danger"></i>';						
                }
            }
    				
            $i++;
    	}//foreach
    	My_Comun::armarGrid($registros,$grid);
    }//function
    
    public function agregarAction(){
        $this->_helper->layout->disableLayout();
        $this->view->llave = My_Comun::aleatorio(20);
		
        $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' where 1 = 1 ', ' estado asc');


        if($_POST["id"]!="0"){
            $this->view->registro=My_Comun::obtenerSQL("empresa", "id", $_POST["id"]);
            $this->view->muni = My_Comun::obtenerSQL("municipios",'id_municipio',$this->view->registro->municipio_id);
//            print_r($this->view->muni); exit; 
        }
    }//function

    public function obtieneMunicipiosAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $municipios = My_Comun::obtenerFiltroSQL('municipios', ' where estado_id = '.$_POST['id'],' nombre_municipio asc');
        $opciones = '<option value="">Escoge un municipio</option>';
        
        foreach ($municipios as $municipio) {
            if ($municipio->nombre_municipio != '') {
                $opciones.= '<option value="'.$municipio->id_municipio.'">'.utf8_encode($municipio->nombre_municipio).'</option>';
            }
        }
            echo($opciones);
    }//guardar

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "Empresa";
        	$bitacora[0]["campo"] = "razon_social";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agrega empresa";
        	$bitacora[0]["editar"] = "Editar empresa";

            $preId = My_Comun::guardarSQL("empresa", $_POST, $_POST["id"], $bitacora);

            echo($preId);
    }//guardar
	
    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Empresa";
        $bitacora[0]["campo"] = "razon_social";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar empresa";
        $bitacora[0]["deshabilitar"] = "Deshabilitar empresa";
        $bitacora[0]["habilitar"] = "Habilitar empresa";
			
        echo My_Comun::eliminarSQL("empresa", $_POST["id"], $bitacora);
    }//function 

    public function empresaUsuarioAction(){

        $usuario = My_Comun::obtenerSQL('usuario', 'id', Zend_Auth::getInstance()->getIdentity()->id);
        $persona = My_Comun::obtenerSQL('persona', 'id', $usuario->persona_id);
//        print_r('Aqui'.$usuario);
        $this->view->registro = My_Comun::obtenerSQL('empresa', 'id', $persona->empresa_id);
          $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' where 1 = 1 ', ' estado asc');
            if ($this->view->registro->municipio_id != '') {
                $this->view->municipio = My_Comun::obtenerSQL('municipios', 'id_municipio', $this->view->registro->municipio_id);

            }
    }//function

}//class
?>