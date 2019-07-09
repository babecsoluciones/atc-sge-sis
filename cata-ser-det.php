<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();
$select = "SELECT * FROM CatServicios WHERE eCodServicio = ".$_GET['val'];
$rsCliente = mysql_query($select);
$rCliente = mysql_fetch_array($rsCliente);
?>
<div class="row">
                            <div class="col-lg-12">
                                <h2 class="title-1 m-b-25">Detalles del Paquete</h2>
                                
                                    <table class="table table-responsive table-top-campaign">
                                        <tr>
                                            <td>Nombre</td>
                                            <td><?=$rCliente{'tNombre'}?></td>
                                            <td>Descripci&oacute;n</td>
                                            <td align="left"><?=nl2br($rCliente{'tDescripcion'})?></td>
                                        </tr>
                                        <tr>
                                            <td>Precio de Venta</td>
                                            <td colspan="4">$<?=$rCliente{'dPrecioVenta'}?></td>
                                        </tr>
                                        
                                    </table>
                                
                            </div>
	
							<div class="col-lg-12 card">
                                
                                 <!--tabs-->
        <?
    $select = "SELECT * FROM CatTiposInventario ORDER BY tNombre DESC";
           $rsTipos = mysql_query($select);
           $tipos = array();
           while($rTipo = mysql_fetch_array($rsTipos))
           {
               $tipos[] = array('eCodTipoInventario'=>$rTipo{'eCodTipoInventario'},'tNombre'=>$rTipo{'tNombre'});
           }
    ?>
        
        <div class="custom-tab">

											<nav>
												<div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                    <?
                                                    for($i=0;$i<sizeof($tipos);$i++)
                                                    {
                                                        ?>
                                                    <a class="nav-item nav-link <?=($i==0) ? 'active' : ''?>" id="custom-nav-home-tab" data-toggle="tab" href="#custom-nav-<?=$tipos[$i]['eCodTipoInventario']?>" role="tab" aria-controls="custom-nav-<?=$tipos[$i]['eCodTipoInventario']?>"
													 aria-selected="true"><?=$tipos[$i]['tNombre']?></a>
                                                    <?
                                                    }
                                                    ?>
												</div>
											</nav>
											<div class="tab-content pl-3 pt-2" id="nav-tabContent">
                                                <?
                                                $b=0;
                                                    for($i=0;$i<sizeof($tipos);$i++)
                                                    {
                                                        ?>
                                                    <div class="tab-pane fade <?=($i==0) ? 'show active' : ''?>" id="custom-nav-<?=$tipos[$i]['eCodTipoInventario']?>" role="tabpanel" aria-labelledby="custom-nav-home-tab">
													
                                                        <!--tablas-->
                                                        
		<div class="table-responsive table--no-card m-b-40" style="max-height:500px; overflow-y: scroll;">
                                    <table width="100%" class="table table-responsive table-top-campaign">
		   <thead>
			   <tr>
				   
			   <td width="95%">Inventario</td>
				   <td>Piezas</td>
			   </tr>
			   </thead>
			   <tbody>
			  <?
											$select = "	SELECT 
															cti.tNombre as tipo, 
															ci.*,
															rti.ePiezas as unidad
														FROM
															CatInventario ci
															INNER JOIN CatTiposInventario cti ON cti.eCodTipoInventario = ci.eCodTipoInventario
															INNER JOIN RelServiciosInventario rti ON rti.eCodInventario=ci.eCodInventario
															WHERE
                                                            ci.eCodTipoInventario = ".$tipos[$i]['eCodTipoInventario'].
															" AND rti.eCodServicio = ".$_GET['val']."
															ORDER BY ci.tNombre ASC";

											$rsPublicaciones = mysql_query($select);
		   									
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												
												?>
											<tr>
												
												<td>
												<?=utf8_decode($rPublicacion{'tipo'})?> | <?=utf8_decode($rPublicacion{'tNombre'})?> | <?=utf8_decode($rPublicacion{'tMarca'})?>
												</td>
												<td>
													<?=$rPublicacion{'unidad'}?>
												</td>
                                            </tr>
											<?
													
											}
											?>
			   </tbody>
										</table>
                                </div>
                                                        <!--tablas-->
                                                        
												</div>
                                                    <?
                                                    }
                                                    ?>
												
											</div>

										</div>
        
        <!--tabs-->
                                
                            </div>
    
   
    
                        </div>