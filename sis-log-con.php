<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
include("../inc/fun-ini.php");

$clSistema = new clSis();
session_start();
$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];
?>


<div class="row">
                            <div class="col-lg-12">
                                <h2 class="title-1 m-b-25">Log de Eventos</h2>
                                    <table class="display" id="tblLogs" width="100%">
                                        <thead>
                                            
                                            <tr>
                                                <th>#</th>
												<th>Fecha</th>
												<th>Usuario</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?
											$select = "	SELECT sl.*,su.tNombre as Usuario FROM SisLogs sl LEFT JOIN SisUsuarios su ON su.eCodUsuario = sl.eCodUsuario ORDER BY sl.eCodEvento DESC";
											$rsPublicaciones = mysql_query($select);
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr>
                                                <td><?=$rPublicacion{'eCodEvento'}?></td>
                                                <td><?=date('d/m/Y H:i',strtotime($rPublicacion{'fhFecha'}))?></td>
                                                <td><?=utf8_decode($rPublicacion{'Usuario'})?></td>
												<td><?=utf8_decode($rPublicacion{'tDescripcion'})?></td>
                                            </tr>
											<?
											}
											?>
                                        </tbody>
                                    </table>
                                
                            </div>
                        </div>