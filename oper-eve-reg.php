<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();
$bAll = $clSistema->validarPermiso($_GET['tCodSeccion']);
$select = "SELECT be.*, (cc.tNombres + ' ' + cc.tApellidos) as tNombre FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente WHERE be.eCodEvento = ".$_GET['eCodEvento'];
$rsPublicacion = mysql_query($select);
$rPublicacion = mysql_fetch_array($rsPublicacion);

//clientes
$select = "	SELECT 
															cc.*, 
											
															su.tNombre as promotor
														FROM
															CatClientes cc
														
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = cc.eCodUsuario".
												($bAll ? "" : " WHERE cc.eCodUsuario = ".$_SESSION['sessionAdmin'][0]['eCodUsuario']).
														" ORDER BY cc.eCodCliente ASC";
$rsClientes = mysql_query($select);

?>
<?
if($_POST)
{
    $res = $clSistema -> registrarEvento();
    
    if($res)
    {
        ?>
            <div class="alert alert-success" role="alert">
                El evento se guard&oacute; correctamente!
            </div>
<script>
setTimeout(function(){
    window.location="?tCodSeccion=cata-eve-con";
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
        <button type="button" class="btn btn-primary" onclick="activarValidacion()" id="btnValidar">
            <i class="fa fa-key" ></i></button>
	<input type="hidden" id="tPasswordVerificador"  style="display:none;" value="<?=base64_decode($_SESSION['sessionAdmin'][0]['tPasswordOperaciones'])?>">
        <input type="password" class="form-control col-md-3" onkeyup="validarUsuario()"  id="tPasswordOperaciones"  style="display:none;" size="8">
        <button type="button" id="btnGuardar" class="btn btn-primary" disabled onclick="validar()"><i class="fa fa-floppy-o"></i> Guardar</button>
	</div>
</div>
<div class="row">
    <div class="col-lg-12">
    <form id="datos" name="datos" action="<?=$_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="eCodEvento" value="<?=$_GET['eCodEvento']?>">
        <input type="hidden" name="eAccion" id="eAccion">
                            <div class="col-lg-12">
								<h2 class="title-1 m-b-25"><?=$_GET['eCodCliente'] ? 'Actualizar ' : '+ '?>Evento</h2>
                                <div class="card col-lg-12">
                                    
                                    <div class="card-body card-block">
                                        <!--campos-->
                                        
           <div class="form-group">
              <label>Cliente</label>
              <select class="form-control" id="eCodCliente" name="eCodCliente">
             <option value="">Seleccione...</option>
                                                        <?
     while($rPaquete = mysql_fetch_array($rsClientes))
{
         ?>
                  <option value="<?=$rPaquete{'eCodCliente'}?>" <?=($rPublicacion{'eCodCliente'}==$rPaquete{'eCodCliente'}) ? 'selected="selected"' : ''?>><?=$rPaquete{'tNombres'}.' '.$rPaquete{'tApellidos'}.' ('.$rPaquete{'tCorreo'}.')'?></option>
                  <?
}
    ?>
       </select>
              
               
           </div>
           <div class="form-group">
              <label>Fecha del Evento</label>
              <input type="text" class="form-control" name="fhFechaEvento" id="fhFechaEvento" placeholder="dd-mm-YYYY" value="<?=$rPublicacion{'fhFechaEvento'} ? date('d-m-Y',strtotime($rPublicacion{'fhFechaEvento'})) : ""?>" >
           </div>
           <div class="form-group">
              <label>Hora de Montaje</label>
              <input type="text" class="form-control" name="tmHoraEvento" id="tmHoraEvento" placeholder="HH:mm" value="<?=date('H:i',strtotime($rPublicacion{'fhFechaEvento'}))?>" >
           </div>
           <div class="form-group">
              <label>Direcci&oacute;n</label>
              <textarea class="form-control" rows="5" style="resize:none;" name="tDireccion" id="tDireccion" maxlength="250"><?=base64_decode(utf8_decode($rPublicacion{'tDireccion'}))?></textarea>
           </div>
           <div class="form-group">
              <label>Observaciones</label>
              <textarea class="form-control" rows="5" style="resize:none;" name="tObservaciones" id="tObservaciones" maxlength="250"><?=base64_decode(utf8_decode($rPublicacion{'tObservaciones'}))?></textarea>
           </div>
           
                                        <!--campos-->
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                
                                    <div class="card card-body card-block table-responsive">
                                        <div class="custom-tab">

											<nav>
												<div class="nav nav-tabs" id="nav-tab" role="tablist">
													<a class="nav-item nav-link active" id="custom-nav-home-tab" data-toggle="tab" href="#custom-nav-home" role="tab" aria-controls="custom-nav-home"
													 aria-selected="true">Paquetes</a>
													<a class="nav-item nav-link" id="custom-nav-profile-tab" data-toggle="tab" href="#custom-nav-profile" role="tab" aria-controls="custom-nav-profile"
													 aria-selected="false">Inventario</a>
												</div>
											</nav>
											<div class="tab-content pl-3 pt-2" id="nav-tabContent">
												<div class="tab-pane fade show active" id="custom-nav-home" role="tabpanel" aria-labelledby="custom-nav-home-tab">
													<div class="table-data__tool">
                                    <div class="table-data__tool-left">
                                        
                                        
                                    </div>
                                    <div class="table-data__tool-right">
                                       <input class="au-input" id='search2' placeholder='Búsqueda rápida...'> 
                                    </div>
                                </div>
                                <div style="max-height:300px; overflow-y: scroll;">
                                    <table class="table table-responsive table-borderless table-top-campaign" id="table2">
                                        <thead>
                                           
                                            <tr>
												<th width="70%">Nombre</th>
                                                <th width="25%">Cantidad</th>
                                                <th class="text-right" width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?
    $opciones = '';
                     for($i=1;$i<=10;$i++){$opciones.='<option value="'.$i.'">'.$i.'</option>';}
											$select = "	SELECT 
															* FROM CatServicios ORDER BY eCodServicio ASC";
											$rsPublicaciones = mysql_query($select);
                                            $b=1;
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr>
												<td><?=($rPublicacion{'tNombre'})?></td>
												<td class="text-right" align="right"> 
                                                   <input type="text" class="form-control" name="eCantidad<?=$b?>" id="eCantidad<?=$b?>" placeholder="10">
                                                </td><td>
                                                    <input type="hidden" id="eCodServicio<?=$b?>" name="eCodServicio<?=$b?>" value="<?=$rPublicacion{'eCodServicio'}?>">
                                                    <input type="hidden" id="tPaquete<?=$b?>" name="tPaquete<?=$b?>" value="<?=$rPublicacion{'tNombre'}?>">
                                                    <input type="hidden" id="dPrecioVenta<?=$b?>" name="dPrecioVenta<?=$b?>" value="<?=$rPublicacion{'dPrecioVenta'}?>">
                                                    <input type="button" class="btn btn-info" onclick="nvaFila(<?=$b?>,1)" value="+">
												</td>
                                            </tr>
											<?
                                                    $b++;
											}
											?>
                                        </tbody>
                                    </table>
                                </div>    
                                                    </div>
                                                <div class="tab-pane fade" id="custom-nav-profile" role="tabpanel" aria-labelledby="custom-nav-profile-tab">
													<div class="table-data__tool">
                                    <div class="table-data__tool-left">
                                        
                                        
                                    </div>
                                    <div class="table-data__tool-right">
                                       <input class="au-input" id='search' placeholder='Búsqueda rápida...'> 
                                    </div>
                                </div>
                                <div style="max-height:300px; overflow-y: scroll;">
                                    <table class="table table-responsive table-borderless table-top-campaign" id="table">
                                        <thead>
                                           
                                            <tr>
												<th width="70%">Nombre</th>
                                                <th width="25%">Cantidad</th>
                                                <th class="text-right" width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?
											$select = "	SELECT 
															cti.tNombre as tipo, 
															ci.*
														FROM
															CatInventario ci
															INNER JOIN CatTiposInventario cti ON cti.eCodTipoInventario = ci.eCodTipoInventario
														ORDER BY ci.tNombre ASC";
											$rsPublicaciones = mysql_query($select);
                                            
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr>
												<td><?=($rPublicacion{'tNombre'})?></td>
												<td class="text-right" align="right"> 
													<input type="text" class="form-control" name="eCantidad<?=$b?>" id="eCantidad<?=$b?>" placeholder="10">
                                                </td><td>
                                                    <input type="hidden" id="eCodServicio<?=$b?>" name="eCodServicio<?=$b?>" value="<?=$rPublicacion{'eCodInventario'}?>">
                                                    <input type="hidden" id="tPaquete<?=$b?>" name="tPaquete<?=$b?>" value="<?=$rPublicacion{'tNombre'}?>">
                                                    <input type="hidden" id="dPrecioVenta<?=$b?>" name="dPrecioVenta<?=$b?>" value="<?=$rPublicacion{'dPrecioVenta'}?>">
                                                <input type="button" class="btn btn-info" onclick="nvaFila(<?=$b?>,2)" value="+">
												</td>
                                            </tr>
											<?
                                                    $b++;
											}
											?>
                                        </tbody>
                                    </table>
                                </div>
												</div>
												</div>
												
												
											</div>

										</div>
                               
                                    <div class="card col-lg-12">
                                        <table class="table table-responsive table-borderless table-top-campaign" id="paquetes">
                                        <thead>
                                            <tr>
                                                <th></th>
												<th>Paquete</th>
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
                                                            rep.eCodTipo
                                                        FROM CatServicios cs
                                                        INNER JOIN RelEventosPaquetes rep ON rep.eCodServicio = cs.eCodServicio AND rep.eCodTipo = 1
                                                        WHERE rep.eCodEvento = ".$_GET['eCodEvento'];
											$rsPublicaciones = mysql_query($select);
                                            
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr id="paq<?=$i?>">
                                                <td><i class="far fa-trash-alt" onclick="deleteRow(<?=$i?>)"></i></td>
                                                <td>
                                                    <input type="hidden" name="eCodServicio<?=$i?>" id="eCodServicio<?=$i?>" value="<?=$rPublicacion{'eCodServicio'}?>">
                                                    <input type="hidden" name="eCantidad<?=$i?>" id="eCantidad<?=$i?>" value="<?=$rPublicacion{'eCantidad'}?>">
                                                    <input type="hidden" name="eCodTipo<?=$i?>" id="eCodTipo<?=$i?>" value="<?=$rPublicacion{'eCodTipo'}?>">
                                                    <input type="hidden" name="totalServ<?=$i?>" id="totalServ<?=$i?>" value="<?=($rPublicacion{'dPrecioVenta'}*$rPublicacion{'eCantidad'})?>">
                                                    <?=$rPublicacion{'tNombre'}?>
                                                </td>
                                                <td>
                                                    <?=$rPublicacion{'eCantidad'}?>
                                                </td>
												<td>$<?=number_format($rPublicacion{'dPrecioVenta'}*$rPublicacion{'eCantidad'},2)?></td>
                                            </tr>
											<?
											$i++;
											}
                                            $select = "	SELECT DISTINCT
															cs.tNombre,
                                                            cs.dPrecioVenta,
                                                            rep.eCodServicio,
                                                            rep.eCantidad,
                                                            rep.eCodTipo
                                                        FROM CatInventario cs
                                                        INNER JOIN RelEventosPaquetes rep ON rep.eCodServicio = cs.eCodInventario and rep.eCodTipo = 2
                                                        WHERE rep.eCodEvento = ".$_GET['eCodEvento'];
											$rsPublicaciones = mysql_query($select);
                                            
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr id="paq<?=$i?>">
                                                <td><i class="far fa-trash-alt" onclick="deleteRow(<?=$i?>)"></i></td>
                                                <td>
                                                    <input type="hidden" name="eCodServicio<?=$i?>" id="eCodServicio<?=$i?>" value="<?=$rPublicacion{'eCodServicio'}?>">
                                                    <input type="hidden" name="eCantidad<?=$i?>" id="eCantidad<?=$i?>" value="<?=$rPublicacion{'eCantidad'}?>">
                                                    <input type="hidden" name="eCodTipo<?=$i?>" id="eCodTipo<?=$i?>" value="<?=$rPublicacion{'eCodTipo'}?>">
                                                    <input type="hidden" name="totalServ<?=$i?>" id="totalServ<?=$i?>" value="<?=($rPublicacion{'dPrecioVenta'}*$rPublicacion{'eCantidad'})?>">
                                                    <?=$rPublicacion{'tNombre'}?>
                                                </td>
                                                <td>
                                                    <?=$rPublicacion{'eCantidad'}?>
                                                </td>
												<td>$<?=number_format($rPublicacion{'dPrecioVenta'}*$rPublicacion{'eCantidad'},2)?></td>
                                            </tr>
											<?
											$i++;
											}
											?>
                                        </tbody>
                                    </table>    
                                    </div>
                                    
      
                                    </div>
                                </div>
                                
                                <div class="col-lg-12">
                                
                                    <div class="card-body card-block">
                                    <table class="table table-borderless ">
                                        <thead>
                                            <tr>
                                                
                                                <td align="right" width="85%">
                                                    
                                                    <input type="hidden" id="totEvento" value="0">
                                                </td>
                                                <td id="totalVenta" align="right">
                                                    
                                                </td>
                                            </tr>
                                            
                                        </thead>
                                    </table>
      
                                    </div>
                                </div>
                                
                            </div>
    </form>
    </div>
                        </div>



<script>

    
    function segmentar()
    {
        var valor = document.getElementById('paquete').value;
        
        var datos = valor.split('-');
        document.getElementById('eCodServicio').value = datos[0];
        document.getElementById('dPrecioVenta').value = datos[1];
    }
    	
    
    //tabla
    function nvaFila(indice,eCodTipo) {
		var codigo		=	!indice ? document.getElementById('eCodServicio')   :   document.getElementById('eCodServicio'+indice);
    	var cantidad	=	!indice ? document.getElementById('eCantidad')      :   document.getElementById('eCantidad'+indice);
        var paquete     =   !indice ? document.getElementById('paquete')        :   document.getElementById('paquete'+indice);
        var dPrecio     =   !indice ? document.getElementById('dPrecioVenta')   :   document.getElementById('dPrecioVenta'+indice);
        var tPaquete    =   !indice ? $( "#paquete option:selected" ).text()    :   document.getElementById('tPaquete'+indice).value;
        
        if(codigo.value!="" && cantidad.value!="")
        {
            var total = dPrecio.value*cantidad.value;
            
		var x = document.getElementById("paquetes").rows.length;
    var table = document.getElementById("paquetes");
    var row = table.insertRow(x);
    row.id="paq"+(x);
    row.innerHTML = '<td><i class="far fa-trash-alt" onclick="deleteRow('+(x-2)+')"></i><input type="hidden" name="eCodTipo'+(x-2)+'" id="eCodTipo'+(x-2)+'" value="'+eCodTipo+'"></td>';
    row.innerHTML += '<td><input type="hidden" name="eCodServicio'+(x-2)+'" id="eCodServicio'+(x-2)+'" value="'+codigo.value+'">'+tPaquete+'</td>';
    row.innerHTML += '<td><input type="hidden" name="eCantidad'+(x-2)+'" id="eCantidad'+(x-2)+'" value="'+cantidad.value+'">'+cantidad.value+'</td>';
	row.innerHTML += '<td id="dTotal'+(x-2)+'"><input type="hidden" id="totalServ'+(x-2)+'" value="'+total.toFixed(2)+'">$'+total.toFixed(2)+'</td>';
   
    calcular();
            
    }
}
    
    function deleteRow(rowid)  {   
    var row = document.getElementById('paq'+rowid);
    row.parentNode.removeChild(row);
        
        calcular();
}

    function calcular()
    {
        var venta = 0;
        var cmbTotal = document.querySelectorAll("[id^=totalServ]");
        cmbTotal.forEach(function(nodo){
            
            venta = parseInt(venta) + parseInt(nodo.value);
            
        });
        
        document.getElementById('totalVenta').innerHTML = "Total: $"+venta.toFixed(2);
    }
    
    function mostrar()
    {
        document.getElementById('inventario').style.display = "inline";
        document.getElementById('oInventario').style.display = "inline";
        document.getElementById('mInventario').style.display = "none";
    }
    function ocultar()
    {
        document.getElementById('inventario').style.display = "none";
        document.getElementById('oInventario').style.display = "none";
        document.getElementById('mInventario').style.display = "inline";
    }
    
    function validar()
    {
        var cmbTotal = document.querySelectorAll("[id^=totalServ]");
        
        var mensaje="<-Favor de revisar la siguiente informaci\u00F3n->\n";
        var bandera = false;
        
        if(!document.getElementById('eCodCliente').value)
            {
                mensaje += "*Cliente\n";
                bandera = true;
            }
        if(!document.getElementById('fhFechaEvento').value)
            {
                mensaje += "*Fecha del evento\n";
                bandera = true;
            }
        if(!document.getElementById('tmHoraEvento').value)
            {
                mensaje += "*Hora de montaje\n";
                bandera = true;
            }
        if(!document.getElementById('tDireccion').value)
            {
                mensaje += "*Ubicaci\u00F3n del evento\n";
                bandera = true;
            }
        if(cmbTotal.length<1)
            {
                mensaje += "*Debes insertar al menos un paquete o extra\n";
                bandera = true;
            }
        
        if(bandera)
            {
                alert(mensaje);
            }
        else
            {
                guardar();
            }
    }
    
    
    calcular();

		</script>