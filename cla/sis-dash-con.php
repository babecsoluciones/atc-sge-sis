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


session_start();

include("../cnx/swgc-mysql.php");
include("../inc/fun-ini.php");
require_once("../cls/cls-sistema.php");

$clSistema = new clSis();
session_start();

$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];

date_default_timezone_set('America/Mexico_City');

$errores = array();

$eventos = array();
$rentas = array();

$data = json_decode( file_get_contents('php://input') );


//Fechas
$fhFechaInicio = $data->fhFechaConsulta ? date('Y-m-d',strtotime($data->fhFechaConsulta)).' 00:00:00' : date('Y-m-d').' 00:00:00';
$fhFechaTermino = $data->fhFechaConsulta ? date('Y-m-d',strtotime($data->fhFechaConsulta)).' 23:59:59' : date('Y-m-d').' 23:59:59';

//consulta eventos
$select = "SELECT be.*, cc.tNombres nombreCliente, cc.tApellidos apellidosCliente,
															su.tNombre as promotor, ce.tNombre Estatus, ce.tIcono FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente
															INNER JOIN CatEstatus ce ON ce.eCodEstatus = be.eCodEstatus
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = be.eCodUsuario
                                                        WHERE
                                                        be.fhFechaEvento >= '$fhFechaInicio' AND be.fhFechaEvento<='$fhFechaTermino'".
                                                        " AND be.eCodEstatus<>4".
                                                        " AND be.eCodTipoDocumento=1".
												        ($bAll ? "" : " AND cc.eCodUsuario = ".$_SESSION['sessionAdmin'][0]['eCodUsuario']).
														" ORDER BY be.fhFechaEvento DESC";





$rsEventos = mysql_query($select);
while($rEvento = mysql_fetch_array($rsEventos))
                                                    {
                                                        $activa = $_SESSION['sessionAdmin'][0]['bAll'] ? '' : 'disabled';
                                                       
                                        $eventos[] = '<div class="col-md-12">
                                <div class="card border border-primary">
                                    <div class="card-header">
                                        <strong class="card-title">
                                         <i class="'.$rEvento{'tIcono'}.'"></i> '.$rEvento{'nombreCliente'}.' '.$rEvento{'apellidosCliente'}.'
                                        </strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><b>Promotor: '.$rEvento{'promotor'}.'</b></p>
                                        <p class="card-text">
                                            Direcci&oacute;n: '.base64_decode($rEvento{'tDireccion'}).'<br>
                                            Estatus: <i class="'.$rEvento{'tIcono'}.'"></i> '.$rEvento{'Estatus'}.'<br>
                                            Fecha: '.date('d/m/Y H:i',strtotime($rEvento{'fhFechaEvento'})).'<br>
                                           
                                        </p>
                                        <br>
                                        <table width="100%">
                                        <tr>
                                            <td align="center">
                                            <button onclick="window.location=\''.obtenerURL().'detalles/catalogo/eventos/'.sprintf("%07d",$rEvento{'eCodEvento'}).'/\'"><i class="fa fa-eye"></i> Detalles</button>
                                            </td>
                                            <td align="center">
                                            <button onclick="agregarTransaccion('.$rEvento{'eCodEvento'}.')" data-toggle="modal" data-target="#myModal"><i class="fas fa-dollar-sign"></i> Nueva Transacci&oacute;n</button>
                                            </td>
                                            <td align="center">
                                                <button onclick="agregarOperador('.$rEvento{'eCodEvento'}.')" data-toggle="modal" data-target="#myModalOperador" <?=$activa?>><i class="fas fa-truck"></i> Asignar Responsable</button>
                                            </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>';
                                                    }

//consulta rentas
$select = "SELECT be.*, cc.tNombres nombreCliente, cc.tApellidos apellidosCliente,
															su.tNombre as promotor, ce.tNombre Estatus, ce.tIcono FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente
															INNER JOIN CatEstatus ce ON ce.eCodEstatus = be.eCodEstatus
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = be.eCodUsuario
                                                        WHERE
                                                        be.fhFechaEvento >= '$fhFechaInicio' AND be.fhFechaEvento<='$fhFechaTermino'".
                                                        " AND be.eCodEstatus<>4".
                                                        " AND be.eCodTipoDocumento=2".
												        ($bAll ? "" : " AND cc.eCodUsuario = ".$_SESSION['sessionAdmin'][0]['eCodUsuario']).
														" ORDER BY be.fhFechaEvento DESC";



$rsEventos = mysql_query($select);
while($rEvento = mysql_fetch_array($rsEventos))
                                                    {
                                                        $activa = $_SESSION['sessionAdmin'][0]['bAll'] ? '' : 'disabled';
                                                       
                                        $rentas[] = '<div class="col-md-12">
                                <div class="card border border-primary">
                                    <div class="card-header">
                                        <strong class="card-title">
                                         <i class="'.$rEvento{'tIcono'}.'"></i> '.$rEvento{'nombreCliente'}.' '.$rEvento{'apellidosCliente'}.'
                                        </strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><b>Promotor: '.$rEvento{'promotor'}.'</b></p>
                                        <p class="card-text">
                                            Direcci&oacute;n: '.base64_decode($rEvento{'tDireccion'}).'<br>
                                            Estatus: <i class="'.$rEvento{'tIcono'}.'"></i> '.$rEvento{'Estatus'}.'<br>
                                            Fecha: '.date('d/m/Y H:i',strtotime($rEvento{'fhFechaEvento'})).'<br>
                                           
                                        </p>
                                        <br>
                                        <table width="100%">
                                        <tr>
                                            <td align="center">
                                            <button onclick="window.location=\''.obtenerURL().'detalles/catalogo/eventos/'.sprintf("%07d",$rEvento{'eCodEvento'}).'/\'"><i class="fa fa-eye"></i> Detalles</button>
                                            </td>
                                            <td align="center">
                                            <button onclick="agregarTransaccion('.$rEvento{'eCodEvento'}.')" data-toggle="modal" data-target="#myModal"><i class="fas fa-dollar-sign"></i> Nueva Transacci&oacute;n</button>
                                            </td>
                                            <td align="center">
                                                <button onclick="agregarOperador('.$rEvento{'eCodEvento'}.')" data-toggle="modal" data-target="#myModalOperador" '.$activa.'><i class="fas fa-truck"></i> Asignar Responsable</button>
                                            </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>';
                                                    }

echo json_encode(array('eventos'=>$eventos,'rentas'=>$rentas));

?>