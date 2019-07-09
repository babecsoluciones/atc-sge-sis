<? header('Access-Control-Allow-Origin: *');  ?>
<? header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method"); ?>
<? header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE"); ?>
<? header("Allow: GET, POST, OPTIONS, PUT, DELETE"); ?>
<? header('Content-Type: application/json'); ?>
<?

if (isset($_SERVER{'HTTP_ORIGIN'})) {
        header("Access-Control-Allow-Origin: {$_SERVER{'HTTP_ORIGIN'}}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

require("../cnx/swgc-mysql.php");

session_start();

$errores = array();

$data = json_decode( file_get_contents('php://input') );

/*Preparacion de variables*/

$codigo = $data->eCodAccion;
$accion = $data->tCodAccion;
switch($accion)
{
    case 'D':
        $insert = "DELETE FROM CatClientes WHERE eCodCliente = ".$codigo;
        break;
    case 'F':
        $insert = "UPDATE CatClientes SET eCodEstatus = 8 WHERE eCodCliente = ".$codigo;
        break;
}
        
        
        $rs = mysql_query($insert);

        if(!$rs)
        {
            $errores[] = 'Error al efectuar la operacion '.mysql_error();
        }

if(!sizeof($errores))
{
    $tDescripcion = "Se ha ".(($accion=="D") ? 'Eliminado' : 'Finalizado')." el cliente código ".sprintf("%07d",$codigo);
    $tDescripcion = "'".utf8_encode($tDescripcion)."'";
    $fecha = "'".date('Y-m-d H:i:s')."'";
    $eCodUsuario = $_SESSION['sessionAdmin'][0]['eCodUsuario'];
    mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fecha, $tDescripcion)");
}

echo json_encode(array("exito"=>((!sizeof($errores)) ? 1 : 0), 'errores'=>$errores));

?>