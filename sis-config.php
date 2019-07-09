<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();

?>
<div class="row">
	<div class="col-lg-12">
	<button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Nueva publicaci&oacute;n</button>
	</div>
</div>
<div class="row">
                            <div class="col-lg-9">
                                <h2 class="title-1 m-b-25">Clientes</h2>
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
											<?
											$select = "	SELECT 
															cc.*, 
															ce.tNombre as estatus
														FROM
															Catclientes cc
														INNER JOIN CatEstatus ce ON cc.eCodEstatus = ce.eCodEstatus ORDER BY cc.eCodCliente DESC LIMIT 0,15";
											$rsPublicaciones = mysql_query($select);
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr>
                                                <td><?=utf8_decode($rPublicacion{'tCodTipo'})?></td>
                                                <td><?=utf8_decode($rPublicacion{'usuario'})?></td>
                                                <td><?=date('d/m/Y',strrotime($rPublicacion{'usuario'}))?></td>
                                                <td class="text-right"><?=utf8_decode($rPublicacion{'tTitulo'})?></td>
                                                <td class="text-right"> - </td>
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