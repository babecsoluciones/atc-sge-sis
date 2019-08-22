<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();
$bAll = $_SESSION['bAll'];
$select = "SELECT be.*, (cc.tNombres + ' ' + cc.tApellidos) as tNombre FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente WHERE be.eCodEvento = ".$_GET['v1'];
$rsPublicacion = mysql_query($select);
$rPublicacion = mysql_fetch_array($rsPublicacion);

$bIVA = $rPublicacion{'bIVA'} ? 1 : 0;

//clientes
$select = "	SELECT 
															cc.*, 
											
															su.tNombre as promotor
														FROM
															CatClientes cc
														
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = cc.eCodUsuario".
												($bAll ? "" : " WHERE cc.eCodUsuario = ".$_SESSION['sessionAdmin']['eCodUsuario']).
														" ORDER BY cc.eCodCliente ASC";
$rsClientes = mysql_query($select);

?>


<link href="dist/easy-autocomplete.min.css" rel="stylesheet" type="text/css">
<script src="lib/jquery-1.11.2.min.js"></script>
<script src="dist/jquery.easy-autocomplete.min.js" type="text/javascript" ></script>

<?
if($_POST)
{
    mysql_query("DELETE FROM RelEventosRutas WHERE eCodEvento=".$_POST['eCodEvento']);
    $update = "INSERT INTO RelEventosRutas (eCodEvento,eCodUsuarioEntrega,eCodUsuarioRecoleccion) VALUES (".$_POST['eCodEvento'].",".$_POST['eCodUsuarioEntrega'].",".$_POST['eCodUsuarioRecoleccion'].")";
    $rsAsignacio = mysql_query($update);
    $res = $rsAsignacion;
    
    if($res)
    {
        ?>
            <div class="alert alert-success" role="alert">
                La informaci&oacute;n se guard&oacute; correctamente!
            </div>
<script>
setTimeout(function(){
    window.location="?tCodSeccion=cata-ren-con";
},2500);
</script>
<?
    }
    else
    {
  ?>
            <div class="alert alert-danger" role="alert">
                Error al procesar la solicitud!
            </div>
<?
    }
}
?>

 
<div class="row">
    <div class="col-lg-12">
    <form id="datos" name="datos" action="<?=$_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="eCodEvento" value="<?=$_GET['v1']?>">
        <input type="hidden" name="eAccion" id="eAccion">
                            <div class="col-lg-12">
								<h2 class="title-1 m-b-25">Detalles de la Renta</h2>
                                <div class="card col-lg-12">
                                    
                                    <div class="card-body card-block">
                                        <!--campos-->
          
                                        
           <div class="form-group">
              <label>Cliente</label>
              
                                                        <?
     while($rPaquete = mysql_fetch_array($rsClientes))
{
         ?>
                  <?=($rPublicacion{'eCodCliente'}==$rPaquete{'eCodCliente'}) ? $rPaquete{'tNombres'}.' '.$rPaquete{'tApellidos'}.' ('.$rPaquete{'tCorreo'}.')' : ''?>
                  <?
}
    ?>
      
              
               
           </div>
           <div class="form-group">
              <label>Fecha del Evento</label>
              <?=date('d/m/Y H:i',strtotime($rPublicacion{'fhFechaEvento'}))?>
           </div>
           <div class="form-group">
              <label>Hora de Montaje</label>
              <?=date('H:i',strtotime($rPublicacion{'tmHoraMontaje'}))?>
           </div>
           <div class="form-group">
              <label>Direcci&oacute;n</label>
              <?=nl2br(base64_decode(utf8_decode($rPublicacion{'tDireccion'})))?>
           </div>
           <div class="form-group">
              <label>Observaciones</label>
              <?=nl2br(base64_decode(utf8_decode($rPublicacion{'tObservaciones'})))?>
           </div>
           
                                        <!--campos-->
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                
                                    <div class="card card-body card-block">
                                    <table class="table table-responsive table-borderless table-top-campaign" id="table" width="100%">
                                        <thead>
                                            
                                            <tr>
												<th width="85%">Paquete</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?
                                            $i = 0;
											$select = "	SELECT DISTINCT
															cs.tNombre,
                                                            cs.dPrecioVenta,
                                                            rep.eCodServicio,
                                                            rep.eCantidad,
                                                            rep.dMonto
                                                        FROM CatServicios cs
                                                        INNER JOIN RelEventosPaquetes rep ON rep.eCodServicio = cs.eCodServicio and rep.eCodTipo = 1
                                                        WHERE rep.eCodEvento = ".$_GET['v1'];
											$rsPublicaciones = mysql_query($select);
                                            
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr id="paq<?=$i?>">
                                                <td align="left">
                                                    <?=$rPublicacion{'tNombre'}?>
                                                    <input type="hidden" name="eCodServicio<?=$i?>" id="eCodServicio<?=$i?>" value="<?=$rPublicacion{'eCodServicio'}?>">
                                                    <input type="hidden" name="eCantidad<?=$i?>" id="eCantidad<?=$i?>" value="<?=$rPublicacion{'eCantidad'}?>">
                                                    <input type="hidden" name="eCodTipo<?=$i?>" id="eCodTipo<?=$i?>" value="<?=$rPublicacion{'eCodTipo'}?>">
                                                </td>
                                                <td align="center">
                                                    <?=$rPublicacion{'eCantidad'}?>
                                                </td>
												<td>$<?=number_format($rPublicacion{'dMonto'},2)?><input type="hidden" id="totalServ<?=$i?>" value="<?=($rPublicacion{'dMonto'})?>"></td>
                                            </tr>
											<?
											$i++;
											}
                                            $select = "	SELECT DISTINCT
															cs.tNombre,
                                                            cs.dPrecioVenta,
                                                            rep.eCodServicio,
                                                            rep.eCantidad,
                                                            rep.dMonto
                                                        FROM CatInventario cs
                                                        INNER JOIN RelEventosPaquetes rep ON rep.eCodServicio = cs.eCodInventario and rep.eCodTipo = 2
                                                        WHERE rep.eCodEvento = ".$_GET['v1'];
											$rsPublicaciones = mysql_query($select);
                                            
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr id="paq<?=$i?>">
                                                
                                                <td align="left">
                                                    <?=$rPublicacion{'tNombre'}?>
                                                    <input type="hidden" name="eCodServicio<?=$i?>" id="eCodServicio<?=$i?>" value="<?=$rPublicacion{'eCodServicio'}?>">
                                                    <input type="hidden" name="eCantidad<?=$i?>" id="eCantidad<?=$i?>" value="<?=$rPublicacion{'eCantidad'}?>">
                                                    <input type="hidden" name="eCodTipo<?=$i?>" id="eCodTipo<?=$i?>" value="<?=$rPublicacion{'eCodTipo'}?>">
                                                </td>
                                                <td align="center">
                                                    <?=$rPublicacion{'eCantidad'}?>
                                                </td>
												<td>$<?=number_format($rPublicacion{'dMonto'},2)?><input type="hidden" id="totalServ<?=$i?>" value="<?=($rPublicacion{'dMonto'})?>"></td>
                                            </tr>
											<?
											$i++;
											}
											?>
											<tr>
												<td> <input type="hidden" id="totEvento" value="0"></td>
											<td colspan="3" align="right" id="totalVenta"></td>
											</tr>
											
											<tr <?=(($bIVA==1) ? '' : 'hidden');?>>
												<td><input type="hidden" id="bIVA" value="<?=$bIVA;?>"></td>
											<td colspan="3" align="right" id="totIVA"></td>
											</tr>
											
											<tr <?=(($bIVA==1) ? '' : 'hidden');?>>
												<td> </td>
											<td colspan="3" align="right" id="totTotal"></td>
											</tr>
                                        </tbody>
                                    </table>
      
                                    </div>
                                </div>
								
								<div class="col-lg-12">
                                
                                    <div class="card card-body card-block">
                                    <table class="table table-responsive table-borderless table-top-campaign">
                                        <thead>
                                            <tr>
                                                <th>#</th>
												<th>Fecha</th>
												<th>Monto</th>
												<th>Forma</th>
                                            </tr>
                                        </thead>
										<tbody>
											<?
											$select = "SELECT bt.fhFecha, bt.dMonto, ctp.tNombre FROM BitTransacciones bt INNER JOIN CatTiposPagos ctp ON ctp.eCodTipoPago = bt.eCodTipoPago WHERE bt.eCodEvento = ".$_GET['v1'];
											$rsTransacciones = mysql_query($select);
											$i = 1;
											while($rTransaccion = mysql_fetch_array($rsTransacciones))
											{
												?>
											<tr>
												<td><?=$i?></td>
												<td><?=date('d/m/Y',strtotime($rTransaccion{'fhFecha'}))?></td>
												<td>$<?=$rTransaccion{'dMonto'}?><input type="hidden" id="abono<?=$i?>" value="<?=$rTransaccion{'dMonto'}?>"></td>
												<td><?=$rTransaccion{'tNombre'}?></td>
											</tr>
											<?
                                                $i++;
											}
											?>
											<tr>
											<td colspan="3" align="right">Total abonado:</td>
												<td id="totAbono"></td>
											</tr>
											<tr>
											<td colspan="3" align="right">Restante:</td>
												<td id="totRestante"></td>
											</tr>
										</tbody>
                                    </table>
      
                                    </div>
                                </div>
                                
                            </div>
    </form>
    </div>
                        </div>

<script> 

    function calcular()
    {
        var venta = 0, abono = 0;
        var cmbTotal = document.querySelectorAll("[id^=totalServ]");
        cmbTotal.forEach(function(nodo){
            
            venta = parseInt(venta) + parseInt(nodo.value);
            
        });
		
		var cmbAbono = document.querySelectorAll("[id^=abono]");
        cmbAbono.forEach(function(nodo){
            
            abono = parseInt(abono) + parseInt(nodo.value);
            
        });
        
        var bIVA = document.getElementById('bIVA').value;
        var total = venta;
        if(bIVA==1)
        {
            document.getElementById('totalVenta').innerHTML = "Subtotal: $"+venta.toFixed(2);
            var dIVA = (venta*0.16);
            var total = venta+dIVA;
            document.getElementById('totIVA').innerHTML = "IVA: $"+dIVA.toFixed(2);
            
            document.getElementById('totTotal').innerHTML = "Total: $"+total.toFixed(2);

        }
        else
        {
           document.getElementById('totalVenta').innerHTML = "Total: $"+venta.toFixed(2); 
        }
        
		document.getElementById('totAbono').innerHTML = "$"+abono.toFixed(2);
		document.getElementById('totRestante').innerHTML = "$"+(total-abono).toFixed(2); 
    }
	
	calcular();

		</script>