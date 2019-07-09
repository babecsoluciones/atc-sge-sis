<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();

$select = "SELECT be.*, (cc.tNombres + ' ' + cc.tApellidos) as tNombre FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente WHERE be.eCodEvento = ".$rEvento{'eCodEvento'};
$rsPublicacion = mysql_query($select);
$rPublicacion = mysql_fetch_array($rsPublicacion);

$bIVA = $rPublicacion{'bIVA'};

//clientes
$select = "	SELECT 
															cc.*, 
											
															su.tNombre as promotor
														FROM
															CatClientes cc
														
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = cc.eCodUsuario
                                                        ORDER BY cc.eCodCliente ASC";
$rsClientes = mysql_query($select);

?>

        <table class="table table-responsive table-borderless table-top-campaign">
            <tr>
                <td >
                  
                                Evento # <?=sprintf("%07d",$rEvento{'eCodEvento'})?><br>
                                Fecha: <?=date('d/m/Y H:i',strtotime($rPublicacion{'fhFechaEvento'}))?><br>
                                Hora de Montaje: <?=$rPublicacion{'tmHoraMontaje'}?>
                          
                </td>
            </tr>
            
            <tr>
                <td>
                   
                                 <?
     while($rPaquete = mysql_fetch_array($rsClientes))
{
         ?>
                  <?=($rPublicacion{'eCodCliente'}==$rPaquete{'eCodCliente'}) ? $rPaquete{'tNombres'}.' '.$rPaquete{'tApellidos'}.' <br>'.$rPaquete{'tCorreo'}.'<br>Tel.'.$rPaquete{'tTelefonoFijo'}.'<br>Cel.'.$rPaquete{'tTelefonoMovil'} : ''?>
                  <?
}
    ?>
                            </td>
            </tr>
            <tr>
                            <td>
                                <?=nl2br(base64_decode(utf8_decode($rPublicacion{'tDireccion'})))?>
                            </td>
               
            </tr>
            
            
            
            <tr>
                <td>
                    Descripci&oacute;n
                </td>
                
              
            </tr>
            
            
											<?
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
                                                        WHERE rep.eCodEvento = ".$rEvento{'eCodEvento'};
											$rsPublicaciones = mysql_query($select);
                                            $dTotalEvento = 0;
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr>
                <td>
                    <b>x<?=$rPublicacion{'eCantidad'}?></b> - <?=utf8_decode($rPublicacion{'tNombre'})?><br>
                   
                   
                    <?
                        $select = "SELECT ci.tNombre, rsi.ePiezas FROM CatInventario ci INNER JOIN RelServiciosInventario rsi ON rsi.eCodInventario=ci.eCodInventario WHERE rsi.eCodServicio = ".$rPublicacion{'eCodServicio'};
                                                $rsDetalle = mysql_query($select);
                                                while($rDetalle = mysql_fetch_array($rsDetalle))
                                                { ?>
                        <b>x<?=$rDetalle{'ePiezas'}?></b> - <?=($rDetalle{'tNombre'})?><br>
                                            <? } ?>
                     
                </td>
                
               
            </tr>
											<?
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
                                                        WHERE rep.eCodEvento = ".$rEvento{'eCodEvento'};
											$rsPublicaciones = mysql_query($select);
                                            
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{ ?>
											<tr class="item">
                <td>
                    <b>x<?=$rPublicacion{'eCantidad'}?></b> - <?=utf8_decode($rPublicacion{'tNombre'})?>
                </td>
                
            </tr>
											<? } ?>
            
            
            
            
        </table>
   
