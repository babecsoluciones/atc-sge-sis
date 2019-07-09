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



$eCodEvento = $data->eCodEventoTransaccion;
    $dMonto = $data->dMonto;
    $fhFecha = "'".date('Y-m-d H:i:s')."'";
    $eCodTipoPago = $data->eCodTipoPago;
    $eCodUsuario = $_SESSION['sessionAdmin'][0]['eCodUsuario'];
    
        $insert = "INSERT INTO BitTransacciones (eCodUsuario,eCodEvento,fhFecha,dMonto,eCodTipoPago) VALUES ($eCodUsuario,$eCodEvento,$fhFecha,$dMonto,$eCodTipoPago)";
    mysql_query($insert);
        
       //$pf = fopen("log.txt","w");
       //fwrite($pf,$insert);
       //fclose($pf);
        
        $tDescripcion = "Se ha registrado una transaccion por ".number_format($dMonto,2)." en el evento ".sprintf("%07d",$eCodEvento);
                $tDescripcion = "'".$tDescripcion."'";
                mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fhFecha, $tDescripcion)");
        
    mysql_query("UPDATE BitEventos SET eCodEstatus = 2 WHERE eCodEvento = ".$eCodEvento);

echo json_encode(array("exito"=>((!sizeof($errores)) ? 1 : 0), 'errores'=>$errores));

?>