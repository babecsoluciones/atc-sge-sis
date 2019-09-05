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

$pf = fopen("log-evento.txt","a");

$errores = array();

$data = json_decode( file_get_contents('php://input') );

$eCodEvento = $data->eCodEvento ? $data->eCodEvento : false;
        $eCodCliente = $data->eCodCliente ? $data->eCodCliente : "NULL";
        $eCodUsuario = $_SESSION['sessionAdmin'][0]['eCodUsuario'];
        $fhFechaEvento = $data->fhFechaEvento ? "'".date('Y-m-d',strtotime($data->fhFechaEvento))." ".$data->tmHoraServicio."'" : "NULL";
        $tmHoraMontaje = "'".date('H:i', strtotime('-2 hours', strtotime($data->tmHoraServicio)))."'";
        //$tmHoraMontaje = $data->tmHoraMontaje ? "'".$data->tmHoraMontaje."'" : "NULL";
        $tDireccion = $data->tDireccion ? "'".base64_encode($data->tDireccion)."'" : "NULL";
        $tObservaciones = $data->tObservaciones ? "'".base64_encode($data->tObservaciones)."'" : "NULL";
        $bHoraExtra = $data->bHoraExtra ? $data->bHoraExtra : 0;
        $dHoraExtra = $data->dHoraExtra ? $data->dHoraExtra : "NULL";
        $eCodEstatus = 1;
        $eCodTipoDocumento = 2;
        $bIVA = $data->bIVA ? $data->bIVA : "NULL";
        
        $fhFecha = "'".date('Y-m-d H:i:s')."'";

        $tDescripcion = "";
        
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
                            fhFecha,
                            bHoraExtra,
                            dHoraExtra)
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
                            $fhFecha,
                            $bHoraExtra,
                            $dHoraExtra)";
            
           
            $tDescripcion .= $query." \n\n";
            
            
            $rsEvento = mysql_query($query);
            if($rsEvento)
            {
                $buscar = mysql_fetch_array(mysql_query("SELECT MAX(eCodEvento) as eCodEvento FROM BitEventos WHERE eCodCliente = $eCodCliente AND eCodUsuario = $eCodUsuario ORDER BY eCodEvento DESC"));
                $eCodEvento = $buscar{'eCodEvento'};
                
                $items = $data->cotizacion;
                
                foreach($items as $cotizacion)
                {
                    $eCodServicio = $cotizacion->eCodServicio;
                    $eCantidad = $cotizacion->eCantidad;
                    $eCodTipo = $cotizacion->eCodTipo;
                    $dMonto = $cotizacion->dMonto;
                    
                    $insert = "INSERT INTO RelEventosPaquetes (eCodEvento, eCodServicio, eCantidad,eCodTipo,dMonto) VALUES ($eCodEvento, $eCodServicio, $eCantidad, $eCodTipo, $dMonto)";
                    
                    $tDescripcion .= $insert." \n\n";
                    
                    $rs = mysql_query($insert);

                    if(!$rs)
                    {
                        $errores[] = 'Error al insertar el producto en la cotizaci贸n '.mysql_error();
                    }
                    
                }
                
                $tDescripcion = "Se ha registrado la renta Codigo ".sprintf("%07d",$eCodEvento);
                $tDescripcion = "'".$tDescripcion."'";
                mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fhFecha, $tDescripcion)");
                
            }
            else
            {
                        $errores[] = 'Error al insertar la cotización del evento '.mysql_error();
            }
        }
        else
        {
            $query = "UPDATE BitEventos SET
                            fhFechaEvento = $fhFechaEvento,
                            tmHoraMontaje = $tmHoraMontaje,
                            tDireccion = $tDireccion,
                            tObservaciones = $tObservaciones,
                            bIVA = $bIVA,
                            bHoraExtra = $bHoraExtra,
                            dHoraExtra = $dHoraExtra
                            WHERE eCodEvento = $eCodEvento";
                            
                            $tDescripcion .= $query." \n\n";
                            
            $rsEvento = mysql_query($query);
            if($rsEvento)
            {
                mysql_query("DELETE FROM RelEventosPaquetes WHERE eCodEvento = $eCodEvento");
                
                $items = $data->cotizacion;
                
                foreach($items as $cotizacion)
                {
                    $eCodServicio = $cotizacion->eCodServicio;
                    $eCantidad = $cotizacion->eCantidad;
                    $eCodTipo = $cotizacion->eCodTipo;
                    $dMonto = $cotizacion->dMonto;
                    
                    $insert = "INSERT INTO RelEventosPaquetes (eCodEvento, eCodServicio, eCantidad,eCodTipo,dMonto) VALUES ($eCodEvento, $eCodServicio, $eCantidad, $eCodTipo, $dMonto)";
                    
                    $rs = mysql_query($insert);
                    
                    $tDescripcion .= $insert." \n\n";

                    if(!$rs)
                    {
                        $errores[] = 'Error al insertar el producto en la cotizaci贸n '.mysql_error();
                    }
                    
                }
                
                $tDescripcion = "Se ha modificado La Renta Codigo ".sprintf("%07d",$eCodEvento);
                $tDescripcion = "'".$tDescripcion."'";
                mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fhFecha, $tDescripcion)");
                
            }
            else
            {
                $errores[] = 'Error al insertar la cotización del evento '.mysql_error();
                fwrite($pf,$tRescripcion."\n --- nuevo evento -- \n\n");
            }
        }

echo json_encode(array("exito"=>((!sizeof($errores)) ? 1 : 0), 'errores'=>$errores));

?>