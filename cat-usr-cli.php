<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");

$clSistema = new clSis();
session_start();
$bAll = $clSistema->validarPermiso($_GET['tCodSeccion']);

?>
                            
                                
                                    <table class="display" id="table">
                                        <thead>
                                            
                                            <tr>
                                                <th></th>
                                                <th colspan="2">Nombre</th>
                                                <th class="text-right">Correo</th>
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
                                                ($_SESSION['sessionAdmin'][0]['bAll'] ? "" : " AND cc.eCodEstatus<> 7").
												($bAll ? "" : " AND cc.eCodUsuario = ".$_SESSION['sessionAdmin'][0]['eCodUsuario']).
														" ORDER BY cc.eCodCliente ASC";
											$rsPublicaciones = mysql_query($select);
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{ ?>
											<tr>
                                                <td>
                                                    <button class="btn btn-info" onclick="asignarParametro(<?=$rPublicacion{'eCodCliente'};?>,'<?=utf8_decode($rPublicacion{'tNombres'}.' '.$rPublicacion{'tApellidos'})?>');" data-dismiss="modal"><i class="far fa-check-square"></i></button>
                                                </td>
                                                <td><i class="<?=$rPublicacion{'estatus'}?>"></i></td>
												<td><?=utf8_decode($rPublicacion{'tTitulo'})?> <?=utf8_decode($rPublicacion{'tNombres'})?></td>
												<td><?=utf8_decode($rPublicacion{'tApellidos'})?></td>
												<td><?=utf8_decode($rPublicacion{'tCorreo'})?></td>
                                            </tr>
											<?
											}
											?>
                                        </tbody>
                                    </table>
                                
                            
                        