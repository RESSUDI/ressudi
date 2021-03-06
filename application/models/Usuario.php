<?php

/**
 * Usuario
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Usuario{

/*    public static function ingresar($usuario){				
        try{
                    return Doctrine_Core::getTable('Usuario')->findOneByUsuario($usuario);
        	}catch(Doctrine_Exception $e){
                    return $e->getMessage();
	   }					
    }
*/
    public static function ingresar($usuario,$contrasena){

        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $sql = "SELECT * FROM usuario WHERE usuario = '".$usuario."' AND contrasena='".$contrasena."'";

        $stmt = sqlsrv_query( $conexion, $sql);
        while( $obj = sqlsrv_fetch_object($stmt)) {
        
            return $obj;       
        }
    }

    public static function guardarPermisos($permisos,$id){
        $conec = new Conexion;
        $conexion = $conec->abreConexion();

        $cadena="";
        foreach($permisos as $permiso){
                $cadena.=(empty($cadena))?$permiso:"|".$permiso;
        }//foreach
        $consulta = "UPDATE dbo.usuario set permisos = '".$cadena."' WHERE id = ".$id;

            $s = sqlsrv_prepare($conexion, $consulta);

            try {
                sqlsrv_execute($s);
            } catch (Exception $e) {
                print_r($e);
                exit;
            }

//      Doctrine_Query::create()->update('Usuario')->set('permisos','?',$cadena)->where('id=?',$id)->execute();
        $regi=My_Comun::obtenerSQL("usuario", "id", $id);
        Bitacora::guardar('Usuario','Permisos de usuario',$regi->nombre);
    }//function


    public static function obtieneZonaUsuario($persona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $sql = "  SELECT z.id
                      FROM usuario u
                      INNER JOIN persona p
                      on p.id = u.persona_id
                      INNER JOIN tipo_usuario tu
                      on tu.id = u.tipo_usuario
                      INNER JOIN empresa e
                      on e.id = p.empresa_id
                      INNER JOIN zona z
                      on z.id = e.zona_id
                    WHERE u.persona_id = ".$persona_id;
        $stmt = sqlsrv_query( $conexion, $sql);
        while( $obj = sqlsrv_fetch_object($stmt)) {
            return $obj; 
        }
        
    }

    public static function obtieneZonasXususario($persona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $sql = "SELECT z.id, z.zona_id, zo.nombre as znombre
                FROM persona_zona z
                INNER JOIN zona zo
                on zo.id = z.zona_id
                WHERE z.persona_id = ".$persona_id." AND zo.status = 1 order by zo.nombre asc";
         $stmt = sqlsrv_query( $conexion, $sql);
         $datos = array();
         while( $obj = sqlsrv_fetch_object($stmt)) {
         
             $datos[] =  $obj;       
         }
         return $datos;
    }

    public static function obtieneZonasTiposXususario($persona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $sql = "SELECT z.id, z.zona_id, zo.nombre as znombre, tp.descripcion, tp.id as tipoID
                FROM persona_zona z
                INNER JOIN zona zo
                on zo.id = z.zona_id
                INNER JOIN tipo_persona tp
                on tp.id = z.tipo_id
                WHERE z.persona_id = ".$persona_id." AND zo.status = 1 order by zo.nombre asc";
         $stmt = sqlsrv_query( $conexion, $sql);
         $datos = array();
         while( $obj = sqlsrv_fetch_object($stmt)) {
         
             $datos[] =  $obj;       
         }
         return $datos;
    }

    public static function obtieneestadosZonasXususario($persona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        // $sql = "  SELECT z.id, z.zona_id, zo.nombre as znombre, es.estado, es.id_estado as estadoId
        $sql = "SELECT DISTINCT es.estado, es.id_estado as estadoId
                FROM persona_zona z
                INNER JOIN zona zo
                on zo.id = z.zona_id
                INNER JOIN estados es
                on es.id_estado = zo.estado_id
                WHERE z.persona_id = ".$persona_id." AND zo.status = 1 order by es.estado asc";
         $stmt = sqlsrv_query( $conexion, $sql);
         $datos = array();
         while( $obj = sqlsrv_fetch_object($stmt)) {
         
             $datos[] =  $obj;       
         }
         return $datos;
    }

    
    public static function ObtienePersonaFromUsuario($usuario_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $sql = "  SELECT u.persona_id
                      FROM usuario u 
                      INNER JOIN persona p
                      on p.id = u.persona_id
                    WHERE u.id = ".$usuario_id;
        $stmt = sqlsrv_query( $conexion, $sql);
        while( $obj = sqlsrv_fetch_object($stmt)) {
            return $obj; 
        }
        
    }

    public static function obtieneUsuarioPersona($persona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $sql = "  SELECT u.id
                      FROM usuario u 
                      INNER JOIN persona p
                      on p.id = u.persona_id
                    WHERE u.persona_id = ".$persona_id;
        $stmt = sqlsrv_query( $conexion, $sql);
        while( $obj = sqlsrv_fetch_object($stmt)) {
            return $obj; 
        }
        
    }

    // public static function obtieneUsuarioUltimo()
    // {
    //     $con = new Conexion;
    //     $conex = $con->abreConexion();
    //     $sql = "  SELECT TOP 1 * FROM usuario ORDER BY id DESC ";
    //     $stmt = sqlsrv_query( $conex, $sql);
    //     $results=;
    //     while( $obj = sqlsrv_fetch_object($stmt)) {
    //         $results = $obj; 
    //     }

    //     // $conec = new Conexion;
    //     // $conexion = $conec->abreConexion();
    //     // $sql = "  SELECT i.id, em.zona_id
    //     // FROM usuario i
    //     // INNER JOIN persona pe
    //     // on i.persona_id = pe.id
    //     // INNER JOIN empresa em
    //     // on em.id = pe.empresa_id
    //     // WHERE i.id = ".$results->id;
    //     // $stmt = sqlsrv_query( $conexion, $sql);
    //     // $results2=;
    //     // while( $obj = sqlsrv_fetch_object($stmt)) {
    //     //     $results2 = $obj; 
    //     // }
    //     // echo("<script>console.log('PHP: ".$results2->zona_id." usuario ".$results2->id."');</script>");

    //     // return $results2; 
    // }

    public static function obtienePersonaZonasByIds($usuario_id, $zona_id)
    {
        $conec = new Conexion;
        $conexion = $conec->abreConexion();
        $sql = "  SELECT z.id, z.zona_id, z.persona_id
                FROM persona_zona z
                WHERE z.persona_id = ".$usuario_id." and z.zona_id = ".$zona_id ;
         $stmt = sqlsrv_query( $conexion, $sql);
         $datos = array();
         while( $obj = sqlsrv_fetch_object($stmt)) {
            // echo("<script>console.log('PHP: $obj->zona_id +  ');</script>");
             $datos[] =  $obj;       
         }
         return $datos;
    }

    public static function guardarSQLpersonaZona($zona_id, $persona_id, $tipo_id){

        $regi=My_Comun::obtenerFiltroSQL("persona_zona");
        $idPer = Zend_Auth::getInstance()->getIdentity()->id;
        // $regi=Usuario::obtienePersonaZonasByIds($user_id, $zona_id);
        $bandera=false;

        foreach ($regi as $pZona){
            if( ($pZona->zona_id == $zona_id && $pZona->persona_id == $persona_id && $pZona->tipo_id == $tipo_id  )  ) {
                echo("<script>console.log('PHP: ".$pZona->zona_id." usuario ".$pZona->persona_id."');</script>");
                $bandera=true;
            }
        }

        if($bandera == false){
            $conec = new Conexion;
            $conexion = $conec->abreConexion();

                $sql ="INSERT INTO dbo.persona_zona([persona_id],[zona_id],[tipo_id]) VALUES ($persona_id, $zona_id, $tipo_id)";
                // echo("<script>console.log('PHP: ".$sql."');</script>");

            $s = sqlsrv_prepare($conexion, $sql);
            
            if( !$s ) {
                // die( print_r( sqlsrv_errors(), true));
                return '<script language="javascript">alert("¡ATENCIÓN! Ocurrió un error inesperado. Contactar al equipo de soporte de RESSUDI.");</script>';
            }

            if( sqlsrv_execute( $s ) === false ) {
                // die( print_r( sqlsrv_errors(), true));
                return '<script language="javascript">alert("¡ATENCIÓN! Ocurrió un error inesperado. Contactar al equipo de soporte de RESSUDI.");</script>';
            }
        }else{
            // echo '<script language="javascript">alert("juas");</script>'; 
                return '<script language="javascript">alert("REGISTRO YA EXISTE");</script>';
        }

        // try {
        //     $iddevuelto =0;
        //     sqlsrv_execute($s);
        //     if( ($errors = sqlsrv_errors() ) != null) {
        //     }
        //     sqlsrv_next_result($s);
        //     $r = sqlsrv_fetch_array($s, SQLSRV_FETCH_ASSOC);

        //     if( $r === false ) {
        //         //descomentar de ser necesario para saber que errores hay
        //         die( print_r( sqlsrv_errors(), true));
        //         return "¡ATENCIÓN! Ocurrió un error inesperado. Contactar al equipo de soporte de RESSUDI.";
        //     }else{
        //         //$stmt = sqlsrv_query( $conexion, $consulta);
        //         $iddevuelto = $r['id'];
        //     }
        //     return $iddevuelto;

        // } catch (Exception $e) {
        //     //descomentar de ser necesario para saber que errores hay
        //             // print_r($e);
        //         exit;
        //     }
           
    }
    
    // public static function guardarSQLpersonaZonaType(){

    //     $data= Usuario::obtieneUsuarioUltimo();
    //     $zona_id = $data->zona_id;
    //     $user_id = $data->id;
    //     $type = "empresa";

    //     $regi=My_Comun::obtenerFiltroSQL("usuario_zona");
    //     // $regi=Usuario::obtienePersonaZonasByIds($user_id, $zona_id);
    //     $bandera=false;

    //     foreach ($regi as $pZona){
    //         if( ($pZona->zona_id == $zona_id && $pZona->usuario_id == $user_id )  ) {
    //             echo("<script>console.log('PHP: ".$pZona->zona_id." usuario ".$pZona->usuario_id."');</script>");
    //             $bandera=true;
    //         }
    //     }

    //     if($bandera == false){
    //         $conec = new Conexion;
    //         $conexion = $conec->abreConexion();

    //             $sql ="INSERT INTO dbo.usuario_zona([usuario_id],[zona_id],[type]) VALUES ($user_id, $zona_id,$type)";
    //             // echo("<script>console.log('PHP: ".$sql."');</script>");

    //         $s = sqlsrv_prepare($conexion, $sql);
            
    //         if( !$s ) {
    //             // die( print_r( sqlsrv_errors(), true));
    //             return '<script language="javascript">alert("¡ATENCIÓN! Ocurrió un error inesperado. Contactar al equipo de soporte de RESSUDI.");</script>';
    //         }

    //         if( sqlsrv_execute( $s ) === false ) {
    //             // die( print_r( sqlsrv_errors(), true));
    //             return '<script language="javascript">alert("¡ATENCIÓN! Ocurrió un error inesperado. Contactar al equipo de soporte de RESSUDI.");</script>';
    //         }
    //     }else{
    //         // echo '<script language="javascript">alert("juas");</script>'; 
    //             return '<script language="javascript">alert("REGISTRO YA EXISTE");</script>';
    //     }
        
	// }


}