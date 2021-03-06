<?php
class Backend_AsignaEncuestaController extends Zend_Controller_Action{
    public function init(){
        $this->view->headScript()->appendFile('/js/backend/comun.js?');
        $this->view->headScript()->appendFile('/js/backend/asigna-encuesta.js?'.time());
       
    }//function
 
    public function indexAction(){
        $idPer = Zend_Auth::getInstance()->getIdentity()->persona_id;


        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', ' WHERE status = 1 ', ' nombre asc');
        $this->view->zonas = Usuario::obtieneZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);

        // $this->view->estados = Usuario::obtieneestadosZonasXususario(Zend_Auth::getInstance()->getIdentity()->persona_id);
        // mostrar todos los estados activos
        $this->view->estados = My_Comun::obtenerFiltroSQL('estados', ' WHERE status = 1 ', ' estado asc');

        $this->view->encuestas = My_Comun::obtenerFiltroSQL('encuesta', ' WHERE status = 1 ', ' nombre asc');
        $this->view->categorias = My_Comun::obtenerFiltroSQL('categoria', ' WHERE status = 1 ', ' nombre asc');

    

//        print_r($this->view->encuestas);
//        exit;
    }//function

    public function cambiaZonaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $encuestas = My_Comun::obtenerFiltroSQL('encuesta', ' WHERE status = 1 AND zona_id = '.$_POST['id'], ' nombre asc');

        $lista = '';
        foreach ($encuestas as $encuesta) {
            $lista .= '
                        <div class="col-xs-12">
                            <div class="col-xs-1">
                                <label class="control-label"><input type="checkbox" name="encuesta_id" value="'.$encuesta->id.'"></label>
                            </div>
                            <div class="col-xs-11">
                                <label class="control-label">'.$encuesta->nombre.'</label>
                            </div>
                        </div>
                        ';
        }

        echo $lista;
    }

    public function gridAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sess=new Zend_Session_Namespace('permisos');
        
        $filtro=" 1=1 ";

        $zona=$this->_getParam('zona_id');
        $tipo=$this->_getParam('tipo_id');
        $estado=$this->_getParam('estado_id');
        $encuestas=$this->_getParam('encuestas');

        $encuestas = substr($encuestas,0,-1);
        
        if($zona!='')
        {
            $filtro.=" AND (ze.zona_id = '".$zona."') ";
        }//if

        if($tipo!='')
        {
            $filtro.=" AND (ze.tipo_id = '".$tipo."') ";
            $filtro.=" AND (tp.id = '".$tipo."') ";
        }//if

        if($estado!='')
        {
            $filtro.=" AND (ze.estado_id = '".$estado."') ";
        }//if

        $consulta = "SELECT e.nombre as encuesta, z.nombre as zona, ze.status as status, 
                    e.id as encuId, ze.id as zeID, cat.nombre as catNombre, es.estado, tp.descripcion
                      FROM encuesta e
                      INNER JOIN zona_encuesta ze
                      on e.id = ze.encuesta_id
                      INNER JOIN zona z
                      on z.id = ze.zona_id
                      INNER JOIN tipo_persona tp
                      on ze.zona_id = tp.zona_id 
                      INNER JOIN estados es
                      on z.estado_id = es.id_estado
                      INNER JOIN categoria cat
                      on cat.id = e.categoria_id
                      WHERE ".$filtro;
    
        $registros = My_Comun::registrosGridQuerySQL($consulta);
        $grid=array();
    	$i=0;

        for ($k=0; $k < count($registros['registros']); $k++) 
        {
                
                $grid[$i]['encuesta'] =$registros['registros'][$k]->encuesta;
                $grid[$i]['catNombre'] =$registros['registros'][$k]->catNombre;
                $grid[$i]['zona'] =$registros['registros'][$k]->zona;
                $grid[$i]['estado'] =$registros['registros'][$k]->estado;
                $grid[$i]['tipo'] =$registros['registros'][$k]->descripcion;
                $grid[$i]['status']=(($registros['registros'][$k]->status)?'Habilitado':'Deshabilitado');
                if($registros['registros'][$k]->status == 0){
                    $grid[$i]['habilitar'] = '<span onclick="cambiaStatus('.$registros['registros'][$k]->zeID.', '.$registros['registros'][$k]->status.' );" title="Cambia"><i class="boton fa fa-check-square-o fa-lg text-danger"></i></span>';
                    $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->zeID.', 1 );" title="Remueve"><i class="boton fa fa-times-circle fa-lg azul"></i></span>';
    
                }else{
                    $grid[$i]['habilitar'] = '<span onclick="cambiaStatus('.$registros['registros'][$k]->zeID.', '.$registros['registros'][$k]->status.' );" title="Cambia"><i class="boton fa fa-check-square-o fa-lg azul"></i></span>';
                $grid[$i]['eliminar'] = '<span onclick="eliminar('.$registros['registros'][$k]->zeID.', 1 );" title="Remueve"><i class="boton fa fa-times-circle fa-lg azul"></i></span>';

                }
                
                // $grid[$i]['encuId'] =$registros['registros'][$k]->encuId;


            $i++;
    	}//foreach
    	My_Comun::armarGrid($registros,$grid);
    }//function

    public function guardarAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
		
        	$bitacora = array();
        	$bitacora[0]["modelo"] = "AsignaEncuesta";
        	$bitacora[0]["campo"] = "id";
        	$bitacora[0]["id"] = $_POST["id"];
        	$bitacora[0]["agregar"] = "Agrega categoría";
        	$bitacora[0]["editar"] = "Editar categoría";

            if ($_POST['zona_id'] != '') {
                
                // Encuesta::inactiveEncuestasAsignadas($_POST['zona_id']);
                // Encuesta::eliminaEncuestasAsignadas($_POST['zona_id']);
            }

            $data = array();


//             foreach ($_POST['encuesta_id'] as $key) {
//                 $data[] = $key;
// //                $data['zona_id'] = $_POST['zona_id'];
//             }
//             foreach ($data as $key2) {
//                 $data2 = array();
//                 $data2['encuesta_id'] = $key2;
//                 $data2['zona_id'] = $_POST['zona_id'];
//                 //print_r($data2);
//                 $preId = My_Comun::guardarSQL("zona_encuesta", $data2, $data2["id"], $bitacora);
//             }
//  //               exit;
            $preId = My_Comun::guardarSQL("zona_encuesta", $_POST, 0, $bitacora);

            echo($preId);
    }//guardar

    function eliminarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Categoría";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar categoría";
        $bitacora[0]["deshabilitar"] = "Deshabilitar categoría";
        $bitacora[0]["habilitar"] = "Habilitar categoría";
			
        echo Encuesta::eliminaEncuestasAsignadasByEncuesta( $_POST["id"]);
    }//function

    function statusAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
        $bitacora = array();
        $bitacora[0]["modelo"] = "Categoría";
        $bitacora[0]["campo"] = "nombre";
        $bitacora[0]["id"] = $_POST["id"];
        $bitacora[0]["eliminar"] = "Eliminar categoría";
        $bitacora[0]["deshabilitar"] = "Deshabilitar categoría";
        $bitacora[0]["habilitar"] = "Habilitar categoría";
			
        echo Encuesta::changeStatusEncuestasAsignadasByEncuesta( $_POST["id"], $_POST["status"]);
    }//function

    
    public function onChangeCategoriaAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // $estado = $_POST["estado"];
  
        $categoria=$this->_getParam('categoria');
        $filtro = "WHERE status = 1";
          
        if($categoria!='')
        {
            $filtro.=" AND (categoria_id = $categoria) ";
        }
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        $encuestas = My_Comun::obtenerFiltroSQL('encuesta', $filtro, ' nombre asc');
        echo json_encode($encuestas);
    }

    public function onChangeEstadoAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // $estado = $_POST["estado"];
  
        $estado=$this->_getParam('estado');
        $filtro = "WHERE status = 1";
          
        if($estado!='')
        {
            $filtro.=" AND (estado_id = $estado) ";
        }
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        $encuestas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        echo json_encode($encuestas);
    }

    public function onChangeZonaAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // $estado = $_POST["estado"];
  
        $zona=$this->_getParam('zona');
        $filtro = "WHERE status = 1";
          
        if($zona!='')
        {
            $filtro.=" AND (zona_id = $zona) ";
        }
        // $this->view->zonas = My_Comun::obtenerFiltroSQL('zona', $filtro, ' nombre asc');
        $zonas = My_Comun::obtenerFiltroSQL('tipo_persona', $filtro, ' descripcion asc');
        echo json_encode($zonas);
    }

    
	
    // function eliminarAction()
    // {
    //     $this->_helper->layout->disableLayout();
    //     $this->_helper->viewRenderer->setNoRender(TRUE);
        
			
    //     $bitacora = array();
    //     $bitacora[0]["modelo"] = "Categoría";
    //     $bitacora[0]["campo"] = "nombre";
    //     $bitacora[0]["id"] = $_POST["id"];
    //     $bitacora[0]["eliminar"] = "Eliminar categoría";
    //     $bitacora[0]["deshabilitar"] = "Deshabilitar categoría";
    //     $bitacora[0]["habilitar"] = "Habilitar categoría";
			
    //     echo My_Comun::eliminarSQL("categoria", $_POST["id"], $bitacora);
    // }//function

}//class


?>