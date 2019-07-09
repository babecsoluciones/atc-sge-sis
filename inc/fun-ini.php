<?php
include("../cnx/swgc-mysql.php");

session_start();

date_default_timezone_set('America/America/Mexico_City');


	function obtenerURL()
	{
		$select = "SELECT tValor FROM SisVariables WHERE tNombre = 'tURL'";
        $rCFG = mysql_fetch_array(mysql_query($select));
        return $rCFG{'tValor'};
	}
	
    function generarUrl($seccion, $bServidor = true,$accion,$codigo)
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
        
        $url = ((!$accion) ? $rAccion{'tNombre'} : $accion ).'/'.$rTipo{'tNombre'}.'/'.$rSeccion{'tNombre'}.'/'.($codigo ? $codigo.'/' : '');
        
        $servidor = obtenerURL();
        
        return ($bServidor ? $servidor : '').$url;
    }

    function obtenerScript()
    {   
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tAccion']."'";
        $rAccion = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tTipo']."'";
        $rTipo = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tSeccion']."'";
        $rSeccion = mysql_fetch_array(mysql_query($select));
        
        $url = $rTipo{'tBase'}.'-'.$rSeccion{'tBase'}.'-'.$rAccion{'tBase'};
        
        return $url;
    }

    /* Validamos permisos */

    function validarEliminacion($seccion)
	{
		$bAll = $_SESSION['sessionAdmin'][0]['bAll'];
		$select = 	"SELECT * FROM SisSeccionesPerfiles ".
					($bAll ? "" : " WHERE eCodPerfil = ".$_SESSION['sessionAdmin'][0]['eCodPerfil']." AND tCodSeccion = '".$seccion."'");
		
		$rsSeccion = mysql_query($select);
		$rSeccion = mysql_fetch_array($rsSeccion);
		if($rSeccion{'bDelete'} || $bAll)
		{
			return true;
		}
        else
        {
            return false;
        }
	}

    /*Buscamos los botones*/

    function botones($codigo)
    {
        $tCodSeccion = obtenerScript();
        
        $join = $_SESSION['sessionAdmin'][0]['bAll'] ? 'LEFT ' : 'INNER ';
        
        $select = "SELECT DISTINCT
                        ssb.tCodPadre,ssb.tCodSeccion,ssb.tEtiqueta, sb.*, ssb.tCodBoton boton, ssb.tFuncion funcion 
                    FROM 
                        SisSeccionesBotones ssb 
                    INNER JOIN SisBotones sb on sb.tCodBoton=ssb.tCodBoton 
                    INNER JOIN SisSeccionesPerfiles ssp ON ssp.tCodSeccion=ssb.tCodPadre 
                    INNER JOIN SisSeccionesPerfiles sss on sss.tCodSeccion=ssb.tCodSeccion 
                    WHERE
                    1=1 ".
                    ($_SESSION['sessionAdmin'][0]['bAll'] ? "" :" AND sss.eCodPerfil = ".$_SESSION['sessionAdmin'][0]['eCodPerfil']).
                    " AND ssb.tCodPadre = '".$tCodSeccion."'
                    ORDER BY ssb.tCodPadre ASC, ssb.ePosicion ASC";
        
        //echo $select;
        $rsBotones = mysql_query($select);
        while($rBoton = mysql_fetch_array($rsBotones))
        { 
        $accion = ($rBoton{'tAccion'}) ? $rBoton{'tAccion'} : false;
         
        if($rBoton{'tCodBoton'}!="CO")   
         { $seccion = generarUrl($rBoton{'tCodSeccion'},true, $accion,(($codigo) ? sprintf("%07d",$codigo) : false)); }
        else
         { $seccion = generarUrl($rBoton{'tCodSeccion'},true, $accion); }
            
         if($rBoton{'tCodBoton'}!="CO")   
         {  $funcion = str_replace(array('url','codigo'),array($seccion,$codigo),$rBoton{'tFuncion'}); }
            else
         {  $funcion = str_replace('url',$seccion,$rBoton{'tFuncion'}); }
         ?>
            <button type="button" class="<?=$rBoton{'tClase'}?>" <?=($rBoton{'bDeshabilitado'}) ? 'disabled' : ''?> onclick="<?=($rBoton{'funcion'}) ? $rBoton{'funcion'} : $funcion?>" id="<?=(($rBoton{'tId'}) ? $rBoton{'tId'} : $rBoton{'tCodBoton'} )?>">
            <?=$rBoton{'tIcono'}?> <?=($rBoton{'tEtiqueta'}) ? $rBoton{'tEtiqueta'} : $rBoton{'tTitulo'}?></button><?=$rBoton{'tHTML'}?>
           <? unset($funcion); ?>
           <? }
    }

    function menuEmergente($codigo)
    {
       
        
        $tCodSeccion = obtenerScript();
        
        $bDelete = validarEliminacion($tCodSeccion);
        
        $join = $_SESSION['sessionAdmin'][0]['bAll'] ? 'LEFT ' : 'INNER ';

        $select = "SELECT DISTINCT
                        ssb.tCodPadre, 
                        ssb.tCodSeccion,
                        ssb.tCodPermiso, 
                        ssb.tTitulo, 
                        ssb.tAccion, 
                        ssb.tFuncion, 
                        ssb.tValor, 
                        ssb.ePosicion
                    FROM 
                        SisSeccionesMenusEmergentes ssb 
                    INNER JOIN SisSeccionesPerfiles ssp ON ssp.tCodSeccion=ssb.tCodPadre 
                    INNER JOIN SisSeccionesPerfiles sss on sss.tCodSeccion=ssb.tCodSeccion 
                    WHERE
                    1=1 ".
                    ($_SESSION['sessionAdmin'][0]['bAll'] ? "" :" AND sss.eCodPerfil = ".$_SESSION['sessionAdmin'][0]['eCodPerfil']).
                    " AND ssb.tCodPadre = '".$tCodSeccion."'
                    ORDER BY ssb.tCodPadre ASC, ssb.ePosicion ASC";
        
       
        //echo $select;
        ?>
                <div class="dropdown" style="width:100%;">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width:100%;">
                <?=sprintf("%07d",$codigo)?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <?
        
        //echo $select;
        $rsBotones = mysql_query($select);
        while($rBoton = mysql_fetch_array($rsBotones))
        { 
        $accion = ($rBoton{'tAccion'}) ? $rBoton{'tAccion'} : false;
        $seccion = generarUrl($rBoton{'tCodSeccion'},true, $accion,sprintf("%07d",$codigo));
        $funcion = str_replace(array('url','codigo'),array($seccion,$codigo),$rBoton{'tFuncion'});
        //$funcion = str_replace('seccion',$rBoton{'tCodSeccion'},$rBoton{'tFuncion'});
        //$funcion = str_replace('codigo',$codigo,$rBoton{'tFuncion'});
        
        $mostrar = ($bDelete) ? '' : 'style="display:none;"';
         ?>
            <a class="dropdown-item" href="#" onclick="<?=$funcion?>" <?=($rBoton{'tCodPermiso'}=="D" ? $mostrar : '')?>><?=$rBoton{'tTitulo'}?></a>
           <? }
               ?></div></div><?
    }
    

?>