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

include("../cnx/swgc-mysql.php");
include("../inc/fun-ini.php");
require_once("../cls/cls-sistema.php");

$clSistema = new clSis();
session_start();

$bAll = $clSistema->validarPermiso(obtenerScript());


date_default_timezone_set('America/Mexico_City');

session_start();

$data = json_decode( file_get_contents('php://input') );

$eCodEvento = $data->eCodEvento ? $data->eCodEvento : $_GET['eCodEvento'];



      $select = "SELECT be.*, (cc.tNombres + ' ' + cc.tApellidos) as tNombre FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente WHERE be.eCodEvento = ".$eCodEvento;
$rsPublicacion = mysql_query($select);
$rPublicacion = mysql_fetch_array($rsPublicacion);


       $detalle = '<table class="table table-borderless " width="100%">
            <tr>
                <td>
                                Evento # '.sprintf("%07d",$eCodEvento).'<br>
                                Fecha: '.date('d/m/Y H:i',strtotime($rPublicacion{'fhFechaEvento'})).'<br>
                                Hora de Montaje: '.($rPublicacion{'tmHoraMontaje'}).' <br>
                                Responsable Entrega: '.($rPublicacion{'tOperadorEntrega'} ? $rPublicacion{'tOperadorEntrega'} : 'Pendiente').'<br>
                                Responsable Recolecci&oacute;n: '.($rPublicacion{'tOperadorRecoleccion'} ? $rPublicacion{'tOperadorRecoleccion'} : 'Pendiente').'
                </td>
            </tr>
            <tr>
                <td>  ';
                                 
     while($rPaquete = mysql_fetch_array($rsClientes))
{ 
                  $detalle .=(($rPublicacion{'eCodCliente'}==$rPaquete{'eCodCliente'}) ? $rPaquete{'tNombres'}.' '.$rPaquete{'tApellidos'}.' <br>'.$rPaquete{'tCorreo'}.'<br>Tel.'.$rPaquete{'tTelefonoFijo'}.'<br>Cel.'.$rPaquete{'tTelefonoMovil'} : '');
                  
} 
                            $detalle .='</td>
            </tr>
            <tr>
                            <td>
                                '.nl2br(base64_decode(utf8_decode($rPublicacion{'tDireccion'}))).'
                            </td>
            </tr>
            <tr>
                <td>
                    Descripci&oacute;n
                </td>
            </tr>';
											
                                            $i = 0;
											$select = "	SELECT DISTINCT
															cs.tNombre,
                                                            cs.dPrecioVenta,
                                                            rep.eCodServicio,
                                                            rep.eCantidad,
                                                            cs.eCodServicio,
                                                            rep.dMonto
                                                        FROM CatServicios cs
                                                        INNER JOIN RelEventosPaquetes rep ON rep.eCodServicio = cs.eCodServicio and rep.eCodTipo = 1
                                                        WHERE rep.eCodEvento = ".$eCodEvento;
											$rsPaquetes = mysql_query($select);
                                            $dTotalEvento = 0;
											while($rPaquete = mysql_fetch_array($rsPaquetes))
											{
												
											$detalle .='<tr>
                <td>
                    <b>'.$rPaquete{'eCantidad'}.'</b> - '.utf8_decode($rPaquete{'tNombre'}).'<br>';
                    
                        $select = "SELECT 
															cti.tNombre as tipo, 
															ci.*,
															rti.ePiezas as unidad
														FROM
															CatInventario ci
															INNER JOIN CatTiposInventario cti ON cti.eCodTipoInventario = ci.eCodTipoInventario
															INNER JOIN RelServiciosInventario rti ON rti.eCodInventario=ci.eCodInventario
															WHERE
                                                             rti.eCodServicio = ".$rPaquete{'eCodServicio'}."
															ORDER BY ci.tNombre ASC";
                                                $rsDetalle = mysql_query($select);
                                                
                                                if(mysql_num_rows($rsDetalle))
                                                {
                                                    $detalle .= '<ul style="margin-left:15px;">';
                                                    while($rDetalle = mysql_fetch_array($rsDetalle))
                                                    { 
                                                         $detalle .='<li style="margin-left:15px;"><b>x'.($rPaquete{'eCantidad'}*$rDetalle{'unidad'}).'</b> - '.($rDetalle{'tNombre'}).'</li>';
                                                    }
                                                    $detalle .= '</ul>';
                                                }
                                                    $detalle .='</td></tr>';
											
											$i++;
                                                $dTotalEvento = $dTotalEvento + ($rPublicacion{'dMonto'});
											 
                                            }
    
                                            $select = "	SELECT DISTINCT
															cs.tNombre,
                                                            cs.dPrecioVenta,
                                                            rep.eCodServicio,
                                                            rep.eCantidad,
                                                            rep.dMonto
                                                        FROM CatInventario cs
                                                        INNER JOIN RelEventosPaquetes rep ON rep.eCodServicio = cs.eCodInventario and rep.eCodTipo = 2
                                                        WHERE rep.eCodEvento = ".$eCodEvento;
											$rsInventario = mysql_query($select);
                                            
    if(mysql_num_rows($rsInventario))
    {
       while($rInventario = mysql_fetch_array($rsInventario))
											{ 
											$detalle .='<tr>
                                                <td>
                                                    <b>'.$rInventario{'eCantidad'}.'</b> - '.utf8_decode($rInventario{'tNombre'}).'
                                                </td>
                                            </tr>';
											 }  
    }
											
            
        $detalle .='</table>';
                                                
                                            





echo json_encode(array('detalle'=>$detalle));

?>