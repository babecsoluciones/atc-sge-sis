<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
include("../cnx/swgc-mysql.php");
require("../cls/cl-sistema.php");

$clSistema = new clSistema();
 
// Check connection

function generarUrl($seccion, $bServidor = true,$accion,$codigo)
    {
        $base = explode('-',$seccion);
        $tAccion = $base[2];
        $tTipo = $base[0];
        $tSeccion = $base[1];
        
        $select = "SELECT tTitulo, tDirectorio FROM SisSecciones WHERE tCodSeccion = '".$seccion."'";
        $rAccion = mysql_fetch_array(mysql_query($select));
        
        
        $url = ($rAccion['tDirectorio'] ? $rAccion['tDirectorio'] : $_GET['tDirectorio']).'/'.$seccion.'/'.generarTitulo($seccion).'/'.($codigo ? 'v1/'.$codigo.'/' : '');
        
        $servidor = obtenerURL();
        
        return ($bServidor ? $servidor : '').$url;
    }

    function generarTitulo($seccion)
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
        
        $url = $rAccion{'tNombre'}.'-'.$rTipo{'tNombre'}.'-'.$rSeccion{'tNombre'};
        
        
        return $url;
    }
 
if(isset($_REQUEST["term"])){
    // Prepare a select statement
    $select = "	SELECT DISTINCT
						ss.tCodSeccion,
						ss.tTitulo,
						ss.tIcono,
                        ss.ePosicion
					FROM SisSecciones ss".
					($_SESSION['sessionAdmin']['bAll'] ? "" : " INNER JOIN SisSeccionesPerfiles ssp ON ssp.tCodSeccion = ss.tCodSeccion").
					" WHERE
					ss.eCodEstatus = 3 ".
					($_SESSION['sessionAdmin']['bAll'] ? "" :
					" AND
					ssp.eCodPerfil = ".$_SESSION['sessionAdmin']['eCodPerfil']).
                    " AND ss.bPublico IS NULL".
                    " AND ss.tTitulo like '%".$_REQUEST["term"]."%'".
                    " ORDER BY ss.ePosicion ASC";
    
    
            $result = mysql_query($select);
            
    if($result)
    {
            // Check number of rows in the result set
            if(mysql_num_rows($result) > 0){
                // Fetch result rows as an associative array
                while($row = mysql_fetch_array($result)){
                    
                    $url = generarUrl($row{'tCodSeccion'},true);
                    
                    echo "<p><a href='$url'>" . $row["tTitulo"] . "</a></p>";
                }
            } else{
                echo "<p>Sin resultados</p>";
            }
    }
    else
    {
        echo '<p>ERROR: La sentencia '.$sql.' no pudo ser ejecutada '.mysql_error().'</p>';
    }
    
   
}
 
?>