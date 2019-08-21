<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");


$clSistema = new clSis();
session_start();
$bAll = $clSistema->validarPermiso(obtenerScript());
$bDelete = $clSistema->validarEliminacion(obtenerScript());

if($_GET['bEliminar']==1)
{
    $select = "SELECT * FROM BitEventos WHERE eCodCliente = ".$_GET['eCodCliente'];
    $rs = mysql_query($select);
    
    if(mysql_num_rows($rs)>0)
    {
        $update = "UPDATE CatClientes SET eCodEstatus=7 WHERE eCodCliente = ".$_GET['eCodCliente'];
    }
    else
    {
        $update = "DELETE FROM CatClientes WHERE eCodCliente = ".$_GET['eCodCliente'];
    }
    mysql_query($update);
    echo '<script>window.location="?tCodSeccion='.$_GET['tCodSeccion'].'";</script>';
}
?>
<script>
function detalles(eCodCliente)
    {
        window.location="?tCodSeccion=cata-cli-det&eCodCliente="+eCodCliente;
    }
function exportar()
    {
        window.location="gene-cli-xls.php";
    }
</script>
<div class="row">
                            <div class="col-lg-12">
                                <h2 class="title-1 m-b-25">Clientes</h2>
                                
                                    <table class="display" id="cliTable">
                                        <thead>
                                            
                                            <tr>
                                                <th>C&oacute;digo</th>
												<th>E</th>
												<th></th>
                                                <th>Nombre</th>
                                                <th>Apellidos</th>
                                                <th class="text-right">Correo</th>
                                                <th class="text-right">Tel&eacute;fono</th>
												<th class="text-right">Fecha de registro</th>
												<th class="text-right">Promotor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?
											$select = "	SELECT 
															cc.*, 
															ce.tIcono as estatus,
															su.tNombre as promotor
														FROM
															CatClientes cc
														INNER JOIN CatEstatus ce ON cc.eCodEstatus = ce.eCodEstatus
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = cc.eCodUsuario
                                                        WHERE 1=1".
                                                ($_SESSION['sessionAdmin']['bAll'] ? "" : " AND cc.eCodEstatus<> 7").
												($bAll ? "" : " AND cc.eCodUsuario = ".$_SESSION['sessionAdmin']['eCodUsuario']).
														" ORDER BY cc.eCodCliente ASC";
											$rsPublicaciones = mysql_query($select);
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
                                                
												?>
											<tr>
                                                <td><? menuEmergente($rPublicacion{'eCodCliente'}); ?></td>
                                                <td><i class="<?=$rPublicacion{'estatus'}?>"></i></td>
                                                <td><?=utf8_decode($rPublicacion{'tTitulo'})?></td>
												<td><?=utf8_decode($rPublicacion{'tNombres'})?></td>
												<td><?=utf8_decode($rPublicacion{'tApellidos'})?></td>
												<td><?=utf8_decode($rPublicacion{'tCorreo'})?></td>
												<td><?=utf8_decode($rPublicacion{'tTelefonoFijo'})?></td>
                                                <td><?=date('d/m/Y',strtotime($rPublicacion{'fhFechaCreacion'}))?></td>
												<td><?=utf8_decode($rPublicacion{'promotor'})?></td>
                                            </tr>
											<?
											}
											?>
                                        </tbody>
                                    </table>
                                
                            </div>
                        </div>