<?

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("cnx/swgc-mysql.php");

$select = "SELECT tValor FROM SisVariables WHERE tNombre = 'tURL'";
        $rCFG = mysql_fetch_array(mysql_query($select));
        $urlServicio = $rCFG{'tValor'};

function obtenerScript()
    {   
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tFormato']."'";
        $rAccion = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tTipo']."'";
        $rTipo = mysql_fetch_array(mysql_query($select));
        
        $select = "SELECT tBase FROM SisSeccionesReemplazos WHERE tNombre = '".$_GET['tSeccion']."'";
        $rSeccion = mysql_fetch_array(mysql_query($select));
        
        $url = $rTipo{'tBase'}.'-'.$rSeccion{'tBase'}.'-'.$rAccion{'tBase'};
        
        return $url;
    }

$script = $urlServicio.obtenerScript();
//echo $script;
echo '<script>window.location="'.$script.'.php?val='.$_GET['val'].'";</script>';
?>