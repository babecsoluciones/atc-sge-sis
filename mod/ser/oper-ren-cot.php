<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");


$clSistema = new clSis();
session_start();

$bAll = $_SESSION['bAll'];
$bDelete = $_SESSION['bDelete'];

$select = "SELECT be.*, cc.tNombres, cc.tApellidos FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente WHERE be.eCodEvento = ".$_GET['v1'];
$rsPublicacion = mysql_query($select);
$rPublicacion = mysql_fetch_array($rsPublicacion);

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


<div class="row">
    <div class="col-lg-12">
    <form id="datos" name="datos" action="<?=$_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="eCodEvento" id="eCodEvento" value="<?=$_GET['v1']?>">
        <input type="hidden" name="eCodTipoDocumento" value="2">
        <input type="hidden" name="eAccion" id="eAccion">
                            <div class="col-lg-12">
								<h2 class="title-1 m-b-25"><?=$_GET['v1'] ? 'Actualizar ' : '+ '?>Renta</h2>
                                <div id="mostrarTabla" <?=(($_GET['v1'])? 'style="display:none;"' : '')?> class="col-lg-12">
               <table class="display" id="misClientes1" width="100%">
                                        <thead>
                                            
                                            <tr>
                                                <th>Selec.</th>
                                                <th>Nombre</th>
                                                <th>Correo</th>
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
														LEFT JOIN CatEstatus ce ON cc.eCodEstatus = ce.eCodEstatus
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = cc.eCodUsuario
                                                        WHERE 1=1".
                                                ($_SESSION['sessionAdmin']['bAll'] ? "" : " AND cc.eCodEstatus<> 7").
												($bAll ? "" : " AND cc.eCodUsuario = ".$_SESSION['sessionAdmin']['eCodUsuario']).
														" ORDER BY cc.eCodCliente ASC";
          
          
											$rsClientes = mysql_query($select);
											while($rCliente = mysql_fetch_array($rsClientes))
											{ ?>
											<tr>
                                                <td>
                                                    <a href="#" class="btn btn-info" onclick="asignarParametro(<?=$rCliente{'eCodCliente'};?>,'<?=utf8_decode($rCliente{'tNombres'}.' '.$rCliente{'tApellidos'})?>');"><i class="far fa-check-square"></i></a>
                                                </td>
												<td><?=utf8_decode($rCliente{'tTitulo'})?> <?=utf8_decode($rCliente{'tNombres'})?> <?=utf8_decode($rCliente{'tApellidos'})?></td>
												<td><?=utf8_decode($rCliente{'tCorreo'})?></td>
                                            </tr>
											<?
											}
											?>
                                        </tbody>
                                    </table>
                                            </div>
                                <div class="col-lg-12" id="cot1" <?=((!$_GET['v1'])? 'style="display:none;"' : '')?>>
                                    
                                    <div class="card-body card-block">
                                        <!--campos-->
                                        
           <div class="form-group">
              <label> Cliente</label> <a href="#" id="asignarCliente" onclick="mostrarClientes();" style="display:none;"><i class="fas fa-pencil-alt"></i></a>
               <input type="hidden" name="eCodCliente" id="eCodCliente" value="<?=$rPublicacion{'eCodCliente'};?>"> 
               <input type="text" class="form-control" id="tNombreCliente" readonly="readonly" value="<?=$rPublicacion{'tNombres'} . ' '.$rPublicacion{'tApellidos'};?>" <?=((!$_GET['v1'])? 'style="display:none;"' : '')?>> 
               </div>
                                        
        
           
           <div class="form-group">
              <label>I.V.A ?<input type="checkbox" class="form-control" name="bIVA" id="bIVA" value="1" <?=$rPublicacion{'bIVA'} ? "checked" : ""?> onclick="calcular();" ></label>
              
           </div>
           <div class="form-group">
              <label>Fecha del Evento</label>
              <input type="text" class="form-control" name="fhFechaEvento" id="fhFechaEvento" placeholder="dd-mm-YYYY HH:mm" onkeyup="fecha(this.id)" value="<?=$rPublicacion{'fhFechaEvento'} ? date('d-m-Y H:i',strtotime($rPublicacion{'fhFechaEvento'})) : ""?>" >
           </div>
           <div class="form-group">
              <label>Hora de Montaje</label>
              <input type="text" class="form-control" name="tmHoraMontaje" id="tmHoraMontaje" placeholder="HH:mm" onkeyup="hora(this.id)"value="<?=$rPublicacion{'tmHoraMontaje'}?>" >
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
                                <div class="col-lg-12" id="cot2" <?=((!$_GET['v1'])? 'style="display:none;"' : '')?>>
                                
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
													
                                    <table class="display" id="table2" width="100%">
                                        <thead>
                                           
                                            <tr>
												<th width="60%">Nombre</th>
                                                <th width="25%">Cantidad</th>
                                                <th width="15%">Agregar</th>
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
												<td width="60%"><?=($rPublicacion{'tNombre'})?></td>
												<td class="text-right" align="right" width="25%"> 
                                                   <input type="text" class="form-control" name="eCantidad<?=$b?>" id="eCantidad<?=$b?>" placeholder="10">
                                                </td><td width="15%">
                                                    <input type="hidden" id="eCodServicio<?=$b?>" name="eCodServicio<?=$b?>" value="<?=$rPublicacion{'eCodServicio'}?>">
                                                    <input type="hidden" id="tPaquete<?=$b?>" name="tPaquete<?=$b?>" value="<?=($rPublicacion{'tNombre'})?>">
                                                    <input type="hidden" id="dPrecioVenta<?=$b?>" name="dPrecioVenta<?=$b?>" value="<?=$rPublicacion{'dPrecioVenta'}?>">
                                                    <input type="button" class="btn btn-info" onclick="nvaFila(<?=$b?>,1)" value="+">
                                                    <input type="button" class="btn btn-info" onclick="nvaFila(<?=$b?>,1,1)" value="&#x1f381;">
												</td>
                                            </tr>
											<?
                                                    $b++;
											}
											?>
                                        </tbody>
                                    </table>
                                   
                                                    </div>
                                                <div class="tab-pane fade" id="custom-nav-profile" role="tabpanel" aria-labelledby="custom-nav-profile-tab">
													
                                    <table class="display" id="table" width="100%">
                                        <thead>
                                           
                                            <tr>
												<th width="60%">Nombre</th>
                                                <th width="25%">Cantidad</th>
                                                <th width="15%">Agregar</th>
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
												<td width="60%"><?=($rPublicacion{'tNombre'})?></td>
												<td class="text-right" align="right" width="25%"> 
													<input type="text" class="form-control" name="eCantidad<?=$b?>" id="eCantidad<?=$b?>" placeholder="10">
                                                </td><td width="15%">
                                                    <input type="hidden" id="eCodServicio<?=$b?>" name="eCodServicio<?=$b?>" value="<?=$rPublicacion{'eCodInventario'}?>">
                                                    <input type="hidden" id="tPaquete<?=$b?>" name="tPaquete<?=$b?>" value="<?=$rPublicacion{'tNombre'}?>">
                                                    <input type="hidden" id="dPrecioVenta<?=$b?>" name="dPrecioVenta<?=$b?>" value="<?=$rPublicacion{'dPrecioVenta'}?>">
                                                <input type="button" class="btn btn-info" onclick="nvaFila(<?=$b?>,2)" value="+">
                                                <input type="button" class="btn btn-info" onclick="nvaFila(<?=$b?>,2,1)" value="&#x1f381;">
                                                
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
                               
                                    <div class="card col-lg-12">
                                        <table class="display" id="paquetes">
                                        <thead>
                                            <tr>
                                                <th></th>
												<th width="70%">Paquete</th>
                                                <th width="20%">Cantidad</th>
                                                <th width="5%">Precio</th>
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
                                                            rep.eCodTipo,
                                                            rep.dMonto
                                                        FROM CatServicios cs
                                                        INNER JOIN RelEventosPaquetes rep ON rep.eCodServicio = cs.eCodServicio AND rep.eCodTipo = 1
                                                        WHERE rep.eCodEvento = ".$_GET['v1'];
											$rsPublicaciones = mysql_query($select);
                                            
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr id="paq<?=$i?>">
                                                <td><i class="far fa-trash-alt" onclick="deleteRow(<?=$i?>)"></i></td>
                                                <td>
                                                    <input type="hidden" name="cotizacion[<?=$i;?>][eCodServicio]" id="cotizacion[<?=$i;?>][eCodServicio]" value="<?=$rPublicacion{'eCodServicio'}?>">
                                                    <input type="hidden" name="cotizacion[<?=$i;?>][eCantidad]" id="cotizacion[<?=$i;?>][eCantidad]" value="<?=$rPublicacion{'eCantidad'}?>">
                                                    <input type="hidden" name="cotizacion[<?=$i;?>][eCodTipo]" id="cotizacion[<?=$i;?>][eCodTipo]" value="<?=$rPublicacion{'eCodTipo'}?>">
                                                    <input type="hidden" name="totalServ<?=$i?>" id="totalServ<?=$i?>" value="<?=($rPublicacion{'dPrecioVenta'}*$rPublicacion{'eCantidad'})?>">
                                                    <input type="hidden" name="cotizacion[<?=$i;?>][dMonto]" id="cotizacion[<?=$i;?>][dMonto]" value="<?=($rPublicacion{'dMonto'})?>">
                                                    <?=$rPublicacion{'tNombre'}?>
                                                </td>
                                                <td>
                                                    <?=$rPublicacion{'eCantidad'}?>
                                                </td>
												<td>$<?=number_format($rPublicacion{'dMonto'},2)?></td>
                                            </tr>
											<?
											$i++;
											}
                                            $select = "	SELECT DISTINCT
															cs.tNombre,
                                                            cs.dPrecioVenta,
                                                            rep.eCodServicio,
                                                            rep.eCantidad,
                                                            rep.eCodTipo,
                                                            rep.dMonto
                                                        FROM CatInventario cs
                                                        INNER JOIN RelEventosPaquetes rep ON rep.eCodServicio = cs.eCodInventario and rep.eCodTipo = 2
                                                        WHERE rep.eCodEvento = ".$_GET['v1'];
											$rsPublicaciones = mysql_query($select);
                                            
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												?>
											<tr id="paq<?=$i?>">
                                                <td><i class="far fa-trash-alt" onclick="deleteRow(<?=$i?>)"></i></td>
                                                <td>
                                                    <input type="hidden" name="cotizacion[<?=$i;?>][eCodServicio]" id="cotizacion[<?=$i;?>][eCodServicio]" value="<?=$rPublicacion{'eCodServicio'}?>">
                                                    <input type="hidden" name="cotizacion[<?=$i;?>][eCantidad]" id="cotizacion[<?=$i;?>][eCantidad]" value="<?=$rPublicacion{'eCantidad'}?>">
                                                    <input type="hidden" name="cotizacion[<?=$i;?>][eCodTipo]" id="cotizacion[<?=$i;?>][eCodTipo]" value="<?=$rPublicacion{'eCodTipo'}?>">
                                                    <input type="hidden" name="totalServ<?=$i?>" id="totalServ<?=$i?>" value="<?=($rPublicacion{'dPrecioVenta'}*$rPublicacion{'eCantidad'})?>">
                                                    <input type="hidden" name="cotizacion[<?=$i;?>][dMonto]" id="cotizacion[<?=$i;?>][dMonto]" value="<?=($rPublicacion{'dMonto'})?>">
                                                    <?=$rPublicacion{'tNombre'}?>
                                                </td>
                                                <td>
                                                    <?=$rPublicacion{'eCantidad'}?>
                                                </td>
												<td>$<?=number_format($rPublicacion{'dMonto'},2)?></td>
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
                                
                                <div class="col-lg-12" id="cot3" <?=((!$_GET['v1'])? 'style="display:none;"' : '')?>>
                                
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
                                            <tr id="brIVA" hidden>
                                                
                                                <td align="right" width="85%">
                                                    
                                                    
                                                </td>
                                                <td id="totalIVA" align="right">
                                                    
                                                </td>
                                            </tr>
                                            <tr id="brTotal" hidden>
                                                
                                                <td align="right" width="85%">
                                                    
                                                   
                                                </td>
                                                <td id="totalTotal" align="right">
                                                    
                                                </td>
                                            </tr>
                                            
                                        </thead>
                                    </table>
      
                                    </div>
                                </div>
                                
                            </div>
        <input type="hidden" name="eFilas" id="eFilas" value="<?=$i?>">
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
    function nvaFila(indice,eCodTipo,bCortesia) {
		var codigo		=	!indice ? document.getElementById('eCodServicio')   :   document.getElementById('eCodServicio'+indice);
    	var cantidad	=	!indice ? document.getElementById('eCantidad')      :   document.getElementById('eCantidad'+indice);
        var paquete     =   !indice ? document.getElementById('paquete')        :   document.getElementById('paquete'+indice);
        var dPrecio     =   !indice ? document.getElementById('dPrecioVenta')   :   document.getElementById('dPrecioVenta'+indice);
        var tPaquete    =   !indice ? $( "#paquete option:selected" ).text()    :   document.getElementById('tPaquete'+indice).value;
        
        if(codigo.value!="" && cantidad.value!="")
        {
            var total = dPrecio.value*cantidad.value;
            
		var x = document.getElementById("paquetes").rows.length;
            var nIndice = document.getElementById('eFilas').value;
    var table = document.getElementById("paquetes");
    var row = table.insertRow(x);
    row.id="paq"+(nIndice);
    row.innerHTML = '<td style="padding:5px;"><i class="far fa-trash-alt" onclick="deleteRow('+nIndice+')"></i><input type="hidden" name="cotizacion['+nIndice+'][eCodTipo]" id="cotizacion[eCodTipo]['+nIndice+']" value="'+eCodTipo+'"></td>';
    row.innerHTML += '<td><input type="hidden" name="cotizacion['+nIndice+'][eCodServicio]" id="cotizacion['+nIndice+'][eCodServicio]" value="'+codigo.value+'">'+tPaquete+'</td>';
    row.innerHTML += '<td><input type="hidden" name="cotizacion['+nIndice+'][eCantidad]" id="cotizacion['+nIndice+'][eCantidad]" value="'+cantidad.value+'">'+cantidad.value+'</td>';
	row.innerHTML += '<td id="dTotal'+nIndice+'"><input type="hidden" id="cotizacion['+nIndice+'][dMonto]" name="cotizacion['+nIndice+'][dMonto]" value="'+((!bCortesia) ? total.toFixed(2) : 0)+'"><input type="hidden" id="totalServ'+nIndice+'" value="'+((!bCortesia) ? total.toFixed(2) : 0)+'">$'+((!bCortesia) ? total.toFixed(2) : 0)+'</td>';

nIndice++;

    document.getElementById('eFilas').value = nIndice;

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
        var iva;
        var total;
        
        var bIVA = document.getElementById('bIVA');
        
        var leyenda="";
        leyenda = (bIVA.checked==true) ? "Subtotal" : "Total";
        
        var ocultar = (!bIVA.checked) ? true : false;
        
        cmbTotal.forEach(function(nodo){
            
            venta = parseInt(venta) + parseInt(nodo.value);
            iva = (bIVA.checked) ? venta*0.16 : 0;
            total = (bIVA.checked) ? venta*1.16 : venta;
            
        });
        
        
        
        document.getElementById('totalVenta').innerHTML = leyenda+" $"+venta.toFixed(2);
        document.getElementById('totalIVA').innerHTML = "I.V.A. $"+iva.toFixed(2);
        document.getElementById('totalTotal').innerHTML = "Total $"+total.toFixed(2);
        document.getElementById('brIVA').hidden = ocultar;
        document.getElementById('brTotal').hidden = ocultar;
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
        if(!document.getElementById('tmHoraMontaje').value)
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
    
    function fecha(objeto)
{
	var fhFecha = document.getElementById(objeto).value;
  
  if(fhFecha.length==2||fhFecha.length==5)
  {
  	document.getElementById(objeto).value = fhFecha + '-';
  }
    if(fhFecha.length==10)
  {
  	document.getElementById(objeto).value = fhFecha + ' ';
  }
    if(fhFecha.length==13)
  {
  	document.getElementById(objeto).value = fhFecha + ':';
  }
}

function hora(objeto)
{
	var fhFecha = document.getElementById(objeto).value;
  
  if(fhFecha.length==2)
  {
  	document.getElementById(objeto).value = fhFecha + ':';
  }
}
    
    function mostrarClientes()
    {
        document.getElementById('mostrarTabla').style.display='inline';
        document.getElementById('tNombreCliente').style.display = 'none';
        document.getElementById('cot1').style.display = 'none';
        document.getElementById('cot2').style.display = 'none';
        document.getElementById('cot3').style.display = 'none';
    }
    
    calcular();

		</script>