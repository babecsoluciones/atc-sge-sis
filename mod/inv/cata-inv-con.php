<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();

$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];

if($_GET['eCodInventario'])
{
    mysql_query("DELETE FROM CatInventario WHERE eCodInventario =".$_GET['eCodInventario']);
    echo '<script>window.location="?tCodSeccion=cata-inv-con";</script>';
}

//$bAll = $_SESSION['bAll'];
//$bDelete = $_SESSION['bAll'];

?>

<script>

function detalles(eCodCliente)
    {
        window.location="?tCodSeccion=cata-inv-det&eCodInventario="+eCodCliente;
    }
function eliminar(eCodInventario)
    {
        window.location="?tCodSeccion=cata-inv-con&eCodInventario="+eCodInventario;
    }
</script>
<div class="row">
                            <div class="col-lg-12">
                                <h2 class="title-1 m-b-25">Inventario </h2>
                                
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
        <div class="card">
        <div class="custom-tab" style="background-color:rgb(229,229,229)">

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
											<div class="tab-content pl-3 pt-2" id="nav-tabContent" style="background-color:rgb(229,229,229)">
                                                <?
                                                $b=0;
                                                    for($i=0;$i<sizeof($tipos);$i++)
                                                    {
                                                        ?>
                                                    <div class="tab-pane fade <?=($i==0) ? 'show active' : ''?>" id="custom-nav-<?=$tipos[$i]['eCodTipoInventario']?>" role="tabpanel" aria-labelledby="custom-nav-home-tab">
													
                                                        <!--tablas-->
                                                       
                                    <table class="display" id="table<?=$i?>" width="100%">
                                        <thead>
                                            <tr>
                                                <th>C&oacute;digo</th>
												<th>Tipo</th>
												<th>Nombre</th>
                                                <th>Marca</th>
                                                <th>Descripci&oacute;n</th>
                                                <th>Precio Interno</th>
                                                <th>Precio P&uacute;blico</th>
                                                <th>Existencia</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
											<?
											$select = "	SELECT 
															cti.tNombre as tipo, 
															ci.*
														FROM
															CatInventario ci
															INNER JOIN CatTiposInventario cti ON cti.eCodTipoInventario = ci.eCodTipoInventario".
														" WHERE ci.eCodTipoInventario = ".$tipos[$i]['eCodTipoInventario'].
														" ORDER BY ci.tNombre ASC";
											$rsPublicaciones = mysql_query($select);
		   									
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												$select = "SELECT * FROM RelServiciosInventario WHERE eCodInventario = ".$rPublicacion{'eCodInventario'}." AND eCodServicio = ".$_GET['eCodServicio'];
												$rServicio = mysql_fetch_array(mysql_query($select));
												?>
											<tr>
                                                <td><? menuEmergente($rPublicacion{'eCodInventario'}); ?></td>
                                                <td><?=utf8_decode($rPublicacion{'tipo'})?></td>
												<td><?=($rPublicacion{'tNombre'})?></td>
												<td><?=($rPublicacion{'tMarca'})?></td>
												<td><?=substr($rPublicacion{'tDescripcion'},0,50)?>...</td>
												<td>$<?=number_format($rPublicacion{'dPrecioInterno'},2)?></td>
												<td>$<?=number_format($rPublicacion{'dPrecioVenta'},2)?></td>
												<td><?=$rPublicacion{'ePiezas'}?></td>
                                            </tr>
											<?
													$b++;
											}
											?>
                                        </tbody>
                                    </table>
                                
                                                        <!--tablas-->
                                                        
												</div>
                                                    <?
                                                    }
                                                    ?>
												
											</div>

										</div>
        </div>
<!--tabs-->
                                
                            </div>
                        </div>