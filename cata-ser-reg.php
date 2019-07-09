<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();

$select = "SELECT * FROM CatServicios WHERE eCodServicio = ".$_GET['val'];
$rsPublicacion = mysql_query($select);
$rPublicacion = mysql_fetch_array($rsPublicacion);

?>
<?
if($_POST)
{
    $res = $clSistema -> registrarServicio();
    
    if($res)
    {
        ?>
            <div class="alert alert-success" role="alert">
                El paquete se guard&oacute; correctamente!
            </div>
<script>
setTimeout(function(){
    window.location="?tCodSeccion=cata-ser-con";
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
<script type="text/javascript" src="//code.jquery.com/jquery-1.7.1.js"></script>
<script>
function validar()
{
var bandera = false;
var mensaje = "";
var tNombre = document.getElementById("tNombre");
var tDescripcion = document.getElementById("tDescripcion");
var dPrecio = document.getElementById("dPrecio");

    if(!tNombre.value)
    {
        mensaje += "* Nombre\n";
        bandera = true;
    };
    if(!tDescripcion.value)
    {
        mensaje += "* Descripcion\n";
        bandera = true;
    };
    if(!dPrecio.value)
    {
        mensaje += "* Precio\n";
        bandera = true;
    };
    
    
    
    if(!bandera)
    {
        guardar();
    }
    else
    {
        alert("<- Favor de revisar la siguiente información ->\n"+mensaje)
    }
}
    
    function validaNumero(value)
{
    if(value)
        {
            var regex = /[0-9]/;
            if( !regex.test(value) )
            {alert("Error, solo se admiten numeros enteros");}
        }
}
   
</script>
    
<div class="row">
    <div class="col-lg-12">
    <form id="datos" name="datos" action="<?=$_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="eCodServicio" id="eCodServicio" value="<?=$_GET['val']?>">
        <input type="hidden" name="eAccion" id="eAccion">
                            <div class="col-lg-12">
								<h2 class="title-1 m-b-25"><?=$_GET['eCodServicio'] ? 'Actualizar ' : '+ '?>Paquete</h2>
                                <div class="card col-lg-12">
                                    
                                    <div class="card-body card-block">
                                        <!--campos-->
                                        <div class="form-group">
              
           </div>
           <div class="form-group">
              <label>Nombre</label>
              <input type="text" class="form-control" name="tNombre" id="tNombre" placeholder="Nombre" value="<?=utf8_encode($rPublicacion{'tNombre'})?>" >
           </div>
           <div class="form-group">
              <label>Descripci&oacute;n</label>
              <textarea class="form-control" name="tDescripcion" id="tDescripcion" placeholder="Descripci&oacute;n" rows="5" style="resize:none;"><?=utf8_decode($rPublicacion{'tDescripcion'})?></textarea>
           </div>
           <div class="form-group">
              <label>Precio de Venta</label>
              <input type="text" class="form-control" name="dPrecio" id="dPrecio" placeholder="Precio de Venta" value="<?=($rPublicacion{'dPrecioVenta'})?>" >
			   <div><sup>Solo números. Ej. 1200.00</sup></div>
           </div>
           
                                        <!--campos-->
                                    </div>
                                </div>
                            </div>
        
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
        <div class="" style="padding-top:10px;">
            <table class="display" id="table">
                                        <thead>
                                            <tr>
				   <th width="2%"></th>
                   <th width="2%">Tipo</th>
			   <th width="93%">Inventario</th>
				   <th>Piezas</th>
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
														//" WHERE ci.eCodTipoInventario = ".$tipos[$i]['eCodTipoInventario'].
														" ORDER BY cti.tNombre ASC, ci.tNombre ASC";
											$rsPublicaciones = mysql_query($select);
		   									$b = 0;
											while($rPublicacion = mysql_fetch_array($rsPublicaciones))
											{
												$select = "SELECT * FROM RelServiciosInventario WHERE eCodInventario = ".$rPublicacion{'eCodInventario'}." AND eCodServicio = ".$_GET['val'];
												$rServicio = mysql_fetch_array(mysql_query($select));
												?>
											<tr>
												<td>
													
                                                        <input type="checkbox" id="inventario[<?=$b?>][eCodInventario]" name="inventario[<?=$b?>][eCodInventario]" value="<?=$rPublicacion{'eCodInventario'}?>" <?=($rServicio{'eCodInventario'}) ? 'checked' : ''?>>
                                                        
												</td>
                                                <td><?=utf8_decode($rPublicacion{'tipo'})?></td>
												<td>
												<?=($rPublicacion{'tNombre'})?> <?=($rPublicacion{'tMarca'} ? ' | '.$rPublicacion{'tMarca'} : '')?>
												</td>
												<td>
													<input type="text" size="4" name="inventario[<?=$b?>][ePiezas]" id="inventario[<?=$b?>][ePiezas]" class="form-control" placeholder="10" value="<?=$rServicio{'ePiezas'}?>" onblur="validaNumero(this.value);">
												</td>
                                            </tr>
											<?
													$b++;
											}
											?>
                                        </tbody>
                                    </table>
        
        </div>
        <!--tabs-->
		
		
		 		
    </form>
    </div>
                        </div>