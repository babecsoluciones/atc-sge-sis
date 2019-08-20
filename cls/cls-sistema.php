<?php
include("../cnx/swgc-mysql.php");
session_start();
date_default_timezone_set('America/America/Mexico_City');

class clSis
{
	public function __construct()
	{
		$select = "SELECT tValor FROM SisVariables WHERE tNombre = 'tURL'";
        $rCFG = mysql_fetch_array(mysql_query($select));
        $this->url = $rCFG{'tValor'};
	}
	public function iniciarSesion()
	{
		$tCorreo = "'".$_POST['tCorreo']."'";
		$tPasswordAcceso = "'".base64_encode($_POST['tPasswordAcceso'])."'";
		
		$select = "SELECT * FROM SisUsuarios WHERE eCodEstatus=3 AND tCorreo = $tCorreo AND tPasswordAcceso = $tPasswordAcceso";
		$rsUsuario = mysql_query($select);
		$rUsuario = mysql_fetch_array($rsUsuario);
		
		if($rsUsuario)
		{
			$_SESSION['sessionAdmin'] = array($rUsuario);
            $rInicio = mysql_fetch_array(mysql_query("SELECT * FROM SisSeccionesPerfilesInicio WHERE eCodPerfil = ".$rUsuario{'eCodPerfil'}));
            $url = base64_encode($this->generarUrl($rInicio{'tCodSeccion'}));
			return array('exito'=>1,'seccion'=>$url);
		}
		else
		{
			return array('exito'=>0);
		}
	}
	
	public function cargarSeccion($seccion)
	{
		//$res->validarSeccion($seccion);
		//$res = $this->validarSeccion($seccion);
        
        //generar url de archivo
        
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tAccion']."'";
        $rAccion = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tTipo']."'";
        $rTipo = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tSeccion']."'";
        $rSeccion = mysql_fetch_array(mysql_query($select));
        
        $seccion = $rTipo{'tBase'}.'-'.$rSeccion{'tBase'}.'-'.$rAccion{'tBase'};
        
        //incluimos
			$fichero = $seccion.'.php';
			//echo ($fichero);
			return include($fichero);

	}
	
	public function generarMenu()
	{
		$tMenu = '';
        $select = "SELECT * FROM CatTiposSecciones ORDER BY ePosicion ASC";
        $rsTiposSecciones = mysql_query($select);
        while($rTipoSeccion = mysql_fetch_array($rsTiposSecciones))
        {
            /* ****** Cargamos las secciones ******** */
            $select = "	SELECT DISTINCT
						ss.tCodSeccion,
						ss.tTitulo,
						ss.tIcono,
                        ss.ePosicion
					FROM SisSecciones ss".
					($_SESSION['sessionAdmin'][0]['bAll'] ? "" : " INNER JOIN SisSeccionesPerfiles ssp ON ssp.tCodSeccion = ss.tCodSeccion").
					" WHERE
					ss.eCodEstatus = 3
					AND
					ss.tCodPadre = 'sis-dash-con' ".
                    " AND ss.tCodTipoSeccion='".$rTipoSeccion{'tCodTipoSeccion'}."' ".
					($_SESSION['sessionAdmin'][0]['bAll'] ? "" :
					" AND
					ssp.eCodPerfil = ".$_SESSION['sessionAdmin'][0]['eCodPerfil']).
                    " ORDER BY ss.ePosicion ASC";

		      $rsMenus = mysql_query($select);
            
              if(mysql_num_rows($rsMenus))
              {
                  $tMenu .= '<li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fa fa-folder"></i>'.$rTipoSeccion{'tNombre'}.'</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">';
		          while($rMenu = mysql_fetch_array($rsMenus))
		          {
                      $url = $this->generarUrl($rMenu{'tCodSeccion'});
		          	    $activo = ($_GET['tCodSeccion']==$rMenu{'tCodSeccion'}) ? 'class="active"' : '';
		          	    $bArchivo = file_exists($rMenu{'tCodSeccion'}.'.php') ? $url : '#';
		          	    $tMenu .= '<li '.$activo.'>
                                      <a href="'.$this->url.$bArchivo.'">'.utf8_decode($rMenu{'tTitulo'}).'</a>
                                  </li>';
		          }
                  $tMenu .= '</ul></li>';
              }
            /* ****** Cargamos las secciones ******** */
        }
		
		return $tMenu;
	}
	
	public function validarSeccion($seccion)
	{
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($_SESSION['sessionAdmin'][0]['bAll'] ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin'][0]['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		$rSeccion = mysql_fetch_array($rsSeccion);
		return $rSeccion{'tCodSeccion'} ? $rSeccion{'tCodSeccion'} : false;
	}
	
	public function validarEnlace($seccion)
	{
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($_SESSION['sessionAdmin'][0]['bAll'] ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin'][0]['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		if(mysql_num_rows($rsSeccion)<1)
		{
			return false;
		}
        else
        {
            return true;
        }
	}
	
	public function registrarUsuario()
    {
        $eCodUsuario = $_POST['eCodUsuario'] ? $_POST['eCodUsuario'] : false;
        $eCodPerfil = $_POST['eCodPerfil'] ? $_POST['eCodPerfil'] : false;
        $tNombre = $_POST['tNombre'] ? "'".utf8_encode($_POST['tNombre'])."'" : false;
        $tApellidos = $_POST['tApellidos'] ? "'".utf8_encode($_POST['tApellidos'])."'" : false;
        $tPasswordAcceso = $_POST['tPasswordAcceso'] ? "'".base64_encode($_POST['tPasswordAcceso'])."'" : false;
        $tPasswordOperaciones = $_POST['tPasswordOperaciones'] ? "'".base64_encode($_POST['tPasswordOperaciones'])."'" : false;
        $tCorreo = $_POST['tCorreo'] ? "'".$_POST['tCorreo']."'" : false;
        $bAll = $_POST['bAll'] ? 1 : 0;
        
        $fhFechaCreacion = "'".date('Y-m-d H:i:s')."'";
        
        if(!$eCodUsuario)
        {
            $insert = "INSERT INTO SisUsuarios (tNombre, tApellidos, tCorreo, tPasswordAcceso, tPasswordOperaciones,  eCodEstatus, eCodPerfil, fhFechaCreacion,bAll) VALUES ($tNombre, $tApellidos, $tCorreo, $tPasswordAcceso, $tPasswordOperaciones, 3, $eCodPerfil, $fhFechaCreacion,$bAll)";
        }
        else
        {
            $insert = "UPDATE SisUsuarios SET
            tPasswordAcceso = $tPasswordAcceso,
            tPasswordOperaciones = $tPasswordOperaciones,
            eCodPerfil = $eCodPerfil,
            bAll = $bAll
            WHERE
            eCodUsuario = $eCodUsuario";
        }
        
        $rsUsuario = mysql_query($insert);
        
        return $rsUsuario ? true : false;
    }
    
    public function actualizarPerfil()
    {
        $eCodUsuario = $_POST['eCodUsuario'] ? $_POST['eCodUsuario'] : false;
        $tPasswordAcceso = $_POST['tPasswordAcceso'] ? "'".base64_encode($_POST['tPasswordAcceso'])."'" : false;
        $tPasswordOperaciones = $_POST['tPasswordOperaciones'] ? "'".base64_encode($_POST['tPasswordOperaciones'])."'" : false;
        $tCorreo = $_POST['tCorreo'] ? "'".$_POST['tCorreo']."'" : false;
        
        $fhFechaCreacion = "'".date('Y-m-d H:i:s')."'";
        
            $insert = "UPDATE SisUsuarios SET
            tPasswordAcceso = $tPasswordAcceso,
            tPasswordOperaciones = $tPasswordOperaciones
            WHERE
            eCodUsuario = $eCodUsuario";
        
        
        $rsUsuario = mysql_query($insert);
        
        $this->cerrarSesion();
        
        return $rsUsuario ? true : false;
    }
	
	public function cerrarSesion()
	{
		$_SESSION = array();
		$_SESSION['sessionAdmin'] = NULL;
		session_destroy();
	}
	
	//Secciones
	public function validarPermiso($seccion)
	{
        unset($_SESSION['bAll']);
        
		$bAll = $_SESSION['sessionAdmin'][0]['bAll'];
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($bAll ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin'][0]['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		$rSeccion = mysql_fetch_array($rsSeccion);
		if($rSeccion{'bAll'} || $bAll)
		{
            $_SESSION['bAll'] = 1;
			return true;
		}
        else
        {
            $_SESSION['bAll'] = 0;
            return false;
        }
	}
    
    public function validarEliminacion($seccion)
	{
        unset($_SESSION['bDelete']);
		$bAll = $_SESSION['sessionAdmin'][0]['bAll'];
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($bAll ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin'][0]['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		$rSeccion = mysql_fetch_array($rsSeccion);
		if($rSeccion{'bDelete'} || $bAll)
		{
            $_SESSION['bDelete'] = 1;
			return true;
		}
        else
        {
            $_SESSION['bDelete'] = 0;
            return false;
        }
	}
	
	//Clientes
	public function registrarCliente()
    {   
        /*Preparacion de variables*/
        
        $eCodCliente = $_POST['eCodCliente'] ? $_POST['eCodCliente'] : false;
        $tNombre = "'".utf8_encode($_POST['tNombre'])."'";
        $tApellidos = "'".utf8_encode($_POST['tApellidos'])."'";
        $tCorreo = "'".$_POST['tCorreo']."'";
        $tTelefonoFijo = "'".$_POST['tTelefonoFijo']."'";
        $tTelefonoMovil = "'".$_POST['tTelefonoMovil']."'";
        $tComentarios = $_POST['tComentarios'] ? "'".$_POST['tComentarios']."'" : "Sin comentarios";
		$eCodUsuario = $_SESSION['sessionAdmin'][0]['eCodUsuario'];
		$fhFechaCreacion = "'".date('Y-m-d H:i')."'";
        
        if(!$eCodCliente)
        {
            $insert = " INSERT INTO CatClientes
            (
            tNombres,
            tApellidos,
            tCorreo,
            tTelefonoFijo,
            tTelefonoMovil,
            eCodUsuario,
            fhFechaCreacion,
			eCodEstatus,
            tComentarios
			)
            VALUES
            (
            $tNombre,
            $tApellidos,
            $tCorreo,
            $tTelefonoFijo,
            $tTelefonoMovil,
            $eCodUsuario,
            $fhFechaCreacion,
			3,
            $tComentarios
            )";
        }
        else
        {
            $insert = "UPDATE 
                            CatClientes
                        SET
                            tNombres= $tNombre,
                            tApellidos= $tApellidos,
                            tCorreo= $tCorreo,
                            tTelefonoFijo= $tTelefonoFijo,
                            tTelefonoMovil= $tTelefonoMovil,
                            tComentarios = $tComentarios
                            WHERE
                            eCodCliente = ".$eCodCliente;
        }
        
        $rsPublicacion = mysql_query($insert);
        //return $insert;
		//echo $insert;
        return $rsPublicacion ? true : false;
    }
	
	//Servicios
	public function registrarServicio()
    {   
        /*Preparacion de variables*/
        
        $eCodServicio = $_POST['eCodServicio'] ? $_POST['eCodServicio'] : false;
        $tNombre = "'".utf8_encode($_POST['tNombre'])."'";
        $tDescripcion = "'".utf8_encode($_POST['tDescripcion'])."'";
        $dPrecio = $_POST['dPrecio'];
        
        if(!$eCodServicio)
        {
            $insert = " INSERT INTO CatServicios
            (
            tNombre,
            tDescripcion,
            dPrecioVenta
			)
            VALUES
            (
            $tNombre,
            $tDescripcion,
            $dPrecio
            )";
        }
        else
        {
            $insert = "UPDATE 
                            CatServicios
                        SET
                            tNombre= $tNombre,
                            tDescripcion= $tDescripcion,
                            dPrecioVenta= $dPrecio
                            WHERE
                            eCodServicio = ".$eCodServicio;
        }
        
        $rsPublicacion = mysql_query($insert);
        
        $select = "SELECT MAX(eCodServicio) eCodServicio FROM CatServicios";
        $rServicio = mysql_fetch_array(mysql_query($select));
		
		$eCodServicio = $eCodServicio ? $eCodServicio : $rServicio{'eCodServicio'};
		
		mysql_query("DELETE FROM RelServiciosInventario WHERE eCodServicio = $eCodServicio");
	foreach($_POST['eCodInventario'] as $key => $eCodInventario)
	{
		$ePiezas = $_POST['ePiezas'.$key];
		mysql_query("INSERT INTO RelServiciosInventario (eCodServicio, eCodInventario, ePiezas) VALUES ($eCodServicio, $eCodInventario, $ePiezas)");
	}
		
        //return $insert;
		//echo $insert;
        return $rsPublicacion ? true : false;
    }
	
	//Inventario
	public function registrarInventario()
    {   
        /*Preparacion de variables*/
        
        $eCodInventario = $_POST['eCodInventario'] ? $_POST['eCodInventario'] : false;
		$eCodTipoInventario = $_POST['eCodTipoInventario'];
        $tNombre = "'".$_POST['tNombre']."'";
        $tMarca = "'".$_POST['tMarca']."'";
        $tDescripcion = "'".$_POST['tDescripcion']."'";
        $dPrecioInterno = $_POST['dPrecioInterno'];
        $dPrecioVenta = $_POST['dPrecioVenta'];
        $ePiezas = $_POST['ePiezas'];
        $tImagen = "'".$this->base64toImage(base64_encode($_POST['tImagen']))."'";
        
        if(!$eCodInventario)
        {
            $insert = " INSERT INTO CatInventario
            (
			eCodTipoInventario,
            tNombre,
            tMarca,
            tDescripcion,
            dPrecioVenta,
			dPrecioInterno,
			tImagen,
			ePiezas
			)
            VALUES
            (
            $eCodTipoInventario,
            $tNombre,
            $tMarca,
            $tDescripcion,
            $dPrecioVenta,
			$dPrecioInterno,
			$tImagen,
			$ePiezas
            )";
        }
        else
        {
            $insert = "UPDATE 
                            CatInventario
                        SET
                            eCodTipoInventario=$eCodTipoInventario,
            				tNombre=$tNombre,
            				tMarca=$tMarca,
            				tDescripcion=$tDescripcion,
            				dPrecioVenta=$dPrecioVenta,
							dPrecioInterno=$dPrecioInterno,
							tImagen=$tImagen,
							ePiezas=$ePiezas
                            WHERE
                            eCodInventario = ".$eCodInventario;
        }
        
        $rsPublicacion = mysql_query($insert);
        //return $insert;
		//echo $insert;
        return $rsPublicacion ? true : false;
    }
    
    //Eventos
    public function registrarEvento()
    {
        $eCodEvento = $_POST['eCodEvento'] ? $_POST['eCodEvento'] : false;
        $eCodCliente = $_POST['eCodCliente'] ? $_POST['eCodCliente'] : "NULL";
        $eCodUsuario = $_SESSION['sessionAdmin'][0]['eCodUsuario'];
        $fhFechaEvento = $_POST['fhFechaEvento'] ? "'".date('Y-m-d H:i',strtotime($_POST['fhFechaEvento']))."'" : "NULL";
        $tmHoraMontaje = $_POST['tmHoraMontaje'] ? "'".$_POST['tmHoraMontaje']."'" : "NULL";
        $tDireccion = $_POST['tDireccion'] ? "'".base64_encode($_POST['tDireccion'])."'" : "NULL";
        $tObservaciones = $_POST['tObservaciones'] ? "'".base64_encode($_POST['tObservaciones'])."'" : "NULL";
        $eCodEstatus = 1;
        $eCodTipoDocumento = $_POST['eCodTipoDocumento'] ? $_POST['eCodTipoDocumento'] : 1;
        $bIVA = $_POST['bIVA'] ? $_POST['bIVA'] : "NULL";
        
        $fhFecha = "'".date('Y-m-d H:i:s')."'";
        
        if(!$eCodEvento)
        {
            $query = "INSERT INTO BitEventos (
                            eCodUsuario,
							eCodEstatus,
                            eCodCliente,
                            fhFechaEvento,
                            tmHoraMontaje,
                            tDireccion,
                            tObservaciones,
                            eCodTipoDocumento,
                            bIVA,
                            fhFecha)
                            VALUES
                            (
                            $eCodUsuario,
							$eCodEstatus,
                            $eCodCliente,
                            $fhFechaEvento,
                            $tmHoraMontaje,
                            $tDireccion,
                            $tObservaciones,
                            $eCodTipoDocumento,
                            $bIVA,
                            $fhFecha)";
            
           
            
            
            
            $rsEvento = mysql_query($query);
            if($rsEvento)
            {
                $buscar = mysql_fetch_array(mysql_query("SELECT MAX(eCodEvento) as eCodEvento FROM BitEventos WHERE eCodCliente = $eCodCliente AND eCodUsuario = $eCodUsuario ORDER BY eCodEvento DESC"));
                $eCodEvento = $buscar['eCodEvento'];
                
                foreach($_POST as $tCampo => $tValor)
                {
                    $indice=str_replace("eCodServicio","",$tCampo);
                    $eCodServicio = $_POST['eCodServicio'.$indice];
                    $eCantidad = $_POST['eCantidad'.$indice];
                    $eCodTipo = $_POST['eCodTipo'.$indice];
                    $dMonto = $_POST['dMonto'.$indice] ? $_POST['dMonto'.$indice] : ($_POST['totalServ'.$indice]? $_POST['totalServ'.$indice]:0);
                    $insert = "INSERT INTO RelEventosPaquetes (eCodEvento, eCodServicio, eCantidad,eCodTipo,dMonto) VALUES ($eCodEvento, $eCodServicio, $eCantidad, $eCodTipo, $dMonto)";
                    mysql_query($insert);
                    
                }
                
                $tDescripcion = "Se ha registrado el evento ".sprintf("%07d",$eCodEvento);
                $tDescripcion = "'".$tDescripcion."'";
                mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fhFecha, $tDescripcion)");
                
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $query = "UPDATE BitEventos SET
                            fhFechaEvento = $fhFechaEvento,
                            tmHoraMontaje = $tmHoraMontaje,
                            tDireccion = $tDireccion,
                            tObservaciones = $tObservaciones,
                            bIVA = $bIVA
                            WHERE eCodEvento = $eCodEvento";
            $rsEvento = mysql_query($query);
            if($rsEvento)
            {
                mysql_query("DELETE FROM RelEventosPaquetes WHERE eCodEvento = $eCodEvento");
                foreach($_POST as $tCampo => $tValor)
                {
                    $indice=str_replace("eCodServicio","",$tCampo);
                    $eCodServicio = $_POST['eCodServicio'.$indice];
                    $eCantidad = $_POST['eCantidad'.$indice];
                    $eCodTipo = $_POST['eCodTipo'.$indice];
                    $dMonto = $_POST['dMonto'.$indice] ? $_POST['dMonto'.$indice] : ($_POST['totalServ'.$indice]? $_POST['totalServ'.$indice]:0);
                    $insert = "INSERT INTO RelEventosPaquetes (eCodEvento, eCodServicio, eCantidad,eCodTipo,dMonto) VALUES ($eCodEvento, $eCodServicio, $eCantidad, $eCodTipo, $dMonto)";
                    mysql_query($insert);
                    
                }
                $tDescripcion = "Se ha modificado el evento ".sprintf("%07d",$eCodEvento);
                $tDescripcion = "'".$tDescripcion."'";
                mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fhFecha, $tDescripcion)");
                return true;
            }
            else
            {
                return false;
            }
        }
       
    }
    
    private function base64toImage($data)
    {
        $fname = "inv/".uniqid().'.jpg';
        $data1 = explode(',', base64_decode($data));
        $content = base64_decode($data1[1]);
        //$img = filter_input(INPUT_POST, "image");
        //$img = str_replace(array('data:image/png;base64,','data:image/jpg;base64,'), '', base64_decode($data));
        //$img = str_replace(' ', '+', $img);
        //$img = base64_decode($img);
        
        //file_put_contents($fname, $img);
        
        $pf = fopen($fname,"w");
        fwrite($pf,$content);
        fclose($pf);
        
        return $fname;
    }
    
    private function generarUrl($seccion)
    {
        $base = explode('-',$seccion);
        $tAccion = $base[2];
        $tTipo = $base[0];
        $tSeccion = $base[1];
        
        $select = "SELECT tNombre FROM SisSeccionesReemplazos WHERE tBase = '".$tAccion."'";
        $rAccion = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tNombre FROM SisSeccionesReemplazos WHERE tBase = '".$tTipo."'";
        $rTipo = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tNombre FROM SisSeccionesReemplazos WHERE tBase = '".$tSeccion."'";
        $rSeccion = mysql_fetch_array(mysql_query($select));
        
        $url = $rAccion{'tNombre'}.'/'.$rTipo{'tNombre'}.'/'.$rSeccion{'tNombre'}.'/';
        
        return $url;
    }
}



?>