<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();

$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];

?>
<div class="row">
                            <div class="col-lg-9">
                                <h2 class="title-1 m-b-25">Pr&oacute;ximos cursos/eventos</h2>
                                <div class="table-responsive table--no-card m-b-40">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead>
                                            <tr>
												<th>T</th>
                                                <th>Usuario</th>
                                                <th>Fecha</th>
                                                <th class="text-right">T&iacute;tulo</th>
                                                <th class="text-right"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?php
											$select = "SELECT bp.*, su.tNombre as usuario FROM BitPublicaciones bp INNER JOIN SisUsuarios su ON su.eCodUsuario=bp.eCodUsuario ORDER BY bp.fhFechaCurso ASC";
											$rsPublicacion = mysql_query($select);
											while($rPublicacion = mysql_fetch_array($rsPublicacion))
											{
												?>
											<tr>
                                                <td><?=utf8_decode($rPublicacion{'tCodTipo'})?></td>
                                                <td><?=utf8_decode($rPublicacion{'usuario'})?></td>
                                                <td><?=date('d/m/Y',strtotime($rPublicacion{'fhFechaCurso'}))?></td>
                                                <td class="text-right"><?=utf8_decode($rPublicacion{'tTitulo'})?></td>
                                                <td class="text-right">
													<button type="button" class="btn btn-secondary mb-1" onclick="window.location='../detalles.php?id=<?=$rPublicacion{'eCodPublicacion'}?>'">
														<i class="fa fa-eye"></i>
													</button>
												</td>
                                            </tr>
											<?
											}
											?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <h2 class="title-1 m-b-25">&Uacute;ltimas transacciones</h2>
                                <div class="au-card au-card--bg-blue au-card-top-countries m-b-40">
                                    <div class="au-card-inner">
                                        <div class="table-responsive">
                                            <table class="table table-top-countries">
                                                <tbody>
													<tr>
														<td>Codigo</td>
														<td class="text-right">Fecha / hora</td>
													</tr>
													<?
													$select = "SELECT * FROM BitTransacciones ORDER BY ecodTransaccion DESC LIMIT 0,15";
													$rsTransacciones = mysql_query($select);
													while($rTransaccion = mysql_fetch_array($rsTransacciones))
													{
														?>
													<tr>
                                                        <td><?=sprintf("%07d",$rTransaccion{'eCodTransaccion'})?></td>
                                                        <td class="text-right"><?=date('d/m/Y H:i',strtotime($rTransaccion{'fhFecha'}))?></td>
                                                    </tr>
													<?
													}
													?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>