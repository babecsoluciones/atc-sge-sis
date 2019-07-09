<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");


$clSistema = new clSis();
session_start();
$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];

if($_GET['eCodEvento'])
{
    mysql_query("UPDATE BitEventos SET eCodEstatus = ".$_GET['eAccion']." WHERE eCodEvento =".$_GET['eCodEvento']);
    
        $fhFecha = "'".date('Y-m-d H:i:s')."'";
        $tDescripcion = "Se ha ".(($_GET['eAccion']==4) ? 'CANCELADO' : 'FINALIZADO')." el evento ".sprintf("%07d",$_GET['eCodEvento']);
        $tDescripcion = "'".$tDescripcion."'";
        $eCodUsuario = $_SESSION['sessionAdmin'][0]['eCodUsuario'];
        mysql_query("INSERT INTO SisLogs (eCodUsuario, fhFecha, tDescripcion) VALUES ($eCodUsuario, $fhFecha, $tDescripcion)");
    
    echo '<script>window.location="?tCodSeccion=cata-eve-con";</script>';
              
}

?>
<script>
function detalles(codigo)
    {
        window.location="?tCodSeccion=cata-eve-det&eCodEvento="+codigo;
    }
function cancelar(codigo)
    {
        window.location="?tCodSeccion=cata-eve-con&eAccion=4&eCodEvento="+codigo;
    }
function finalizar(codigo)
    {
        window.location="?tCodSeccion=cata-eve-con&eAccion=8&eCodEvento="+codigo;
    }
function ruta(codigo)
    {
        window.location="?tCodSeccion=cata-eve-det&eCodEvento="+codigo+"&bRuta=1";
    }
</script>
<div class="row">
                            <div class="col-lg-12">
                                <h2 class="title-1 m-b-25">Eventos</h2>
                                
                                    <table class="display" id="table" width="100%">
                                        <thead>
                                            
                                            <tr>
                                                <th class="text-right">Codigo</th>
                                                <th class="text-right">E</th>
                                                <th class="text-right">C</th>
												<th class="text-right">Cliente</th>
												<th class="text-right">Fecha Evento (Hora de montaje)</th>
												<th class="text-right">Promotor</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
											<?
											$select = "SELECT be.*, cc.tNombres nombreCliente, cc.tApellidos apellidosCliente, 
															su.tNombre as promotor, ce.tIcono FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente
															INNER JOIN CatEstatus ce ON ce.eCodEstatus = be.eCodEstatus
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = be.eCodUsuario".
                                                " WHERE be.eCodEstatus<>4 AND be.eCodTipoDocumento=1 ".
												($bAll ? "" : " AND cc.eCodUsuario = ".$_SESSION['sessionAdmin'][0]['eCodUsuario']).
														" ORDER BY be.fhFechaEvento DESC";
											
											//echo $select;
											$rsPublicaciones = mysql_query($select);
											
//echo $select;

while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
    $edicion = ($clSistema->validarEnlace('oper-eve-reg'))  ? '' : 'style="display:none;" disabled';
    $detalle = ($clSistema->validarEnlace('cata-eve-det'))  ? '' : 'style="display:none;" disabled';
    $ruta    = ($_SESSION['sessionAdmin'][0]['bAll'])       ? '' : 'style="display:none;" disabled';
    $bloqueo = $bAll ? '' : 'style="display:none;" disabled';
    
    $bCargado = (mysql_num_rows(mysql_query("SELECT * FROM SisRegistrosCargas WHERE eCodEvento = ".$rPublicacion{'eCodEvento'}))) ? true : false;
    
												?>
											<tr>
                                                <td><? menuEmergente($rPublicacion{'eCodEvento'}); ?></td>
                                                <td align="center"><i class="<?=$rPublicacion{'tIcono'}?>"></i></td>
                                                <td align="center"><i class="fas fa-truck-moving" <?=((!$bCargado) ? 'style="display:none;"' : '')?>></i></td>
												<td><?=utf8_decode($rPublicacion{'nombreCliente'}.' '.$rPublicacion{'apellidosCliente'})?></td>
												<td><?=date('d/m/Y H:i', strtotime($rPublicacion{'fhFechaEvento'}))?></td>
												<td><?=utf8_decode($rPublicacion{'promotor'})?></td>
                                                
                                            </tr>
											<?
											}
											?>
                                        </tbody>
                                    </table>
                                
                            </div>
                        </div>