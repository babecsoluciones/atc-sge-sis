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
                    
                    $url = $clSistema->generarUrl($row{'tCodSeccion'})
                    
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