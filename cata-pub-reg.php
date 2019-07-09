<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
$clSistema = new clSis();
session_start();

$select = "SELECT * FROM BitPublicaciones WHERE eCodPublicacion = ".$_GET['eCodPublicacion'];
$rsPublicacion = mysql_query($select);
$rPublicacion = mysql_fetch_array($rsPublicacion);

?>
<?
if($_POST)
{
    $res = $clSistema -> registrarPublicacion();

    if($res)
    {
        ?>
            <div class="alert alert-success" role="alert">
                La publiaci&oacute;n se guard&oacute; correctamente!
            </div>
<script>
setTimeout(function(){
    window.location="?tCodSeccion=cata-pub-con";
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

<script>
function validar()
{
var bandera = false;
var mensaje = "";
var tCodTipo = document.getElementById("tCodTipo");
    var tTitulo = document.getElementById("tTitulo");
    var tDescripcion = document.getElementById("tDescripcion");
    var tTemario = document.getElementById("tTemario");
    var fhFechaPublicacion = document.getElementById("fhFechaPublicacion");
    var fhFechaCurso = document.getElementById("fhFechaCurso");
    var eHoras = document.getElementById("eHoras");
    var bDiploma = document.getElementById("bDiploma");
    var eCodUsuario = document.getElementById("eCodUsuario");
    var tUbicacion = document.getElementById("tUbicacion");
    var ePresencial = document.getElementById("ePresencial");
    var eOnline = document.getElementById("eOnline");
    var bPresencial = document.getElementById("bPresencial");
    var bOnline = document.getElementById("bOnline");
    var dPrecioPresencial = document.getElementById("dPrecioPresencial");
    var dPrecioOnline = document.getElementById("dPrecioOnline");
    var dPrecioModuloPresencial = document.getElementById("dPrecioModuloPresencial");
    var dPrecioModuloOnline = document.getElementById("dPrecioModuloOnline");
    var tSlider = document.getElementById("tSlider");
    var tFlyer = document.getElementById("tFlyer");
    var tPDF = document.getElementById("tPDF");
    if(!tCodTipo.value)
    {
        mensaje += "* Tipo\n";
        bandera = true;
    };
    if(!tTitulo.value)
    {
        mensaje += "* Titulo\n";
        bandera = true;
    };
    if(!tDescripcion.value)
    {
        mensaje += "* Descripcion\n";
        bandera = true;
    };
    if(!tTemario.value)
    {
        mensaje += "* Temario\n";
        bandera = true;
    };
    if(!fhFechaCurso.value)
    {
        mensaje += "* Fecha del Evento\n";
        bandera = true;
    };
    if(!eHoras.value)
    {
        mensaje += "* Horas Totales\n";
        bandera = true;
    };
    if(!tUbicacion.value)
    {
        mensaje += "* Ubicacion\n";
        bandera = true;
    };
    if(!tSlider.value)
    {
        mensaje += "* Imagen del Slider\n";
        bandera = true;
    };
    if(!tFlyer.value)
    {
        mensaje += "* Imagen del Flyer\n";
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
    
function adjuntar(nombre,objeto)
    {
        var fichero = document.getElementById('b'+nombre),
            validador = document.getElementById(objeto);
        
        if(validador.value)
            {
                fichero.value = 1;
            }
        
    }
</script>
    
<div class="row">
    <div class="col-lg-12">
    <form id="datos" name="datos" action="<?=$_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="eCodPublicacion" id="eCodPublicacion" value="<?=$_GET['val']?>">
        <input type="hidden" name="eAccion" id="eAccion">
                            <div class="col-lg-12">
								<h2 class="title-1 m-b-25">+ Publicaci&oacute;n</h2>
                                <div class="card col-lg-12">
                                    
                                    <div class="card-body card-block">
                                        <!--campos-->
                                        <div class="form-group">
              <label>Tipo</label>
              <select class="form-control col-md-4" name="tCodTipo" id="tCodTipo">
                  <option value="">Seleccione</option>
                  <option value="C" <?=($rPublicacion{'tCodTipo'}=="C") ? 'selected="selected"' : ''?>>Curso</option>
                  <option value="E" <?=($rPublicacion{'tCodTipo'}=="E") ? 'selected="selected"' : ''?>>Evento</option>
              </select>
           </div>
           <div class="form-group">
              <label>T&iacute;tulo</label>
              <input type="text" class="form-control" name="tTitulo" id="tTitulo" placeholder="Titulo" value="<?=($rPublicacion{'tTitulo'})?>" >
           </div>
           <div class="form-group">
              <label>Descripci&oacute;n</label>
               <textarea  class="form-control" name="tDescripcion" id="tDescripcion" placeholder="Descripcion" ><?=($rPublicacion{'tDescripcion'})?></textarea>
           </div><div class="form-group">
              <label>Temario</label>
              <textarea class="form-control" name="tTemario" id="tTemario" placeholder="Temario" ><?=($rPublicacion{'tTemario'})?></textarea>
           </div>
           <div class="form-group">
              <label>Fecha del Curso/Evento</label>
              <input type="text" class="form-control col-md-4" name="fhFechaCurso" id="fhFechaCurso" placeholder="dd/mm/AAAA HH:mm" required value="<?=($rPublicacion{'fhFechaCurso'})?>">
           </div>
           <div class="form-group">
              <label>Horas Totales</label>
              <input type="text" class="form-control col-md-4" name="eHoras" id="eHoras" placeholder="Horas Totales" required value="<?=utf8_decode($rPublicacion{'eHoras'})?>">
           </div>
           <div class="form-group">
              <label>
              <input type="checkbox" name="bDiploma" id="bDiploma" placeholder="bDiploma" checked disabled > Diploma ?
              </label>
           </div>
           <div class="form-group">
              <label>Ubicaci&oacute;n</label>
               <textarea class="form-control" name="tUbicacion" id="tUbicacion" placeholder="Ubicacion" required><?=($rPublicacion{'tUbicacion'})?></textarea>
           </div>
           <div class="form-group">
              <label>Asistentes Presenciales</label>
              <input type="text" class="form-control col-md-4" name="ePresencial" id="ePresencial" placeholder="Asistentes Presenciales"  value="<?=utf8_decode($rPublicacion{'ePresencial'})?>">
           </div>
           <div class="form-group">
              <label>Asistentes Online</label>
              <input type="text" class="form-control col-md-4" name="eOnline" id="eOnline" placeholder="Asistentes Online"  value="<?=utf8_decode($rPublicacion{'eOnline'})?>">
           </div>
           <div class="form-group">
              <label>Precio Presencial</label>
              <input type="text" class="form-control col-md-4" name="dPrecioPresencial" id="dPrecioPresencial" placeholder="Precio Presencial"  value="<?=utf8_decode($rPublicacion{'dPrecioPresencial'})?>">
           </div><div class="form-group">
              <label>Precio Online</label>
              <input type="text" class="form-control col-md-4" name="dPrecioOnline" id="dPrecioOnline" placeholder="Precio Online"  value="<?=utf8_decode($rPublicacion{'dPrecioOnline'})?>">
           </div><div class="form-group">
              <label>Precio Modulo Presencial</label>
              <input type="text" class="form-control col-md-4" name="dPrecioModuloPresencial" id="dPrecioModuloPresencial" placeholder="Precio Modulo Presencial"  value="<?=utf8_decode($rPublicacion{'dPrecioModuloPresencial'})?>">
           </div><div class="form-group">
              <label>Precio Modulo Online</label>
              <input type="text" class="form-control col-md-4" name="dPrecioModuloOnline" id="dPrecioModuloOnline" placeholder="Precio Modulo Online"  value="<?=utf8_decode($rPublicacion{'dPrecioModuloOnline'})?>">
           </div><div class="form-group">
              <label>Imagen Slider</label>
              <input type="file" class="form-control" name="tImgSlider" id="tImgSlider" onchange="adjuntar('Slider',this.id)"><input type="hidden" id="bSlider" name="bSlider" value="">
              <input type="hidden" name="tSlider" id="tSlider"  value="<?=utf8_decode($rPublicacion{'tSlider'})?>">
           </div><div class="form-group">
              <label>Imagen Flyer</label>
              <input type="file" class="form-control" name="tImgFlyer" id="tImgFlyer" onchange="adjuntar('Flyer',this.id)"><input type="hidden" id="bFlyer" name="bFlyer" value="">
              <input type="hidden" name="tFlyer" id="tFlyer"  value="<?=utf8_decode($rPublicacion{'tFlyer'})?>">
           </div><div class="form-group">
              <label>Archivo PDF</label>
              <input type="file" class="form-control" id="tArchPDF" name="tArchPDF" onchange="adjuntar('PDF',this.id)" accept="application/pdf, .pdf"><input type="hidden" id="bPDF" name="bPDF" value="">
             <input type="hidden" name="tPdf" value="<?=utf8_decode($rPublicacion{'tPdf'})?>">
           </div>
                                        <!--campos-->
                                    </div>
                                </div>
                            </div>
    </form>
    </div>
                        </div>