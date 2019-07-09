<?php
require_once("cnx/swgc-mysql.php");
require_once("cls/cls-sistema.php");
require_once("lstTiposDocumentos.php");
$clSistema = new clSis();
session_start();

$bAll = $clSistema->validarPermiso($_GET['tCodSeccion']);

$fhFechaInicio = $_POST['fhFechaConsulta'] ? date('Y-m-d',strtotime("+1 month",strtotime($_POST['fhFechaConsulta']))).' 00:00:00' : date('Y-m-d').' 00:00:00';
$fhFechaTermino = $_POST['fhFechaConsulta'] ? date('Y-m-d',strtotime("+1 month",strtotime($_POST['fhFechaConsulta']))).' 23:59:59' : date('Y-m-d').' 23:59:59';

$fhFechaConsulta = $_POST['fhFechaConsulta'] ? date('Y-m-d',strtotime("+1 month",strtotime($_POST['fhFechaConsulta']))).' 00:00:00' : date('Y-m-d').' 00:00:00';

$fhFechaInicio = "'".$fhFechaInicio."'";
$fhFechaTermino = "'".$fhFechaTermino."'";

$select = "SELECT be.*, cc.tNombres nombreCliente, cc.tApellidos apellidosCliente,
															su.tNombre as promotor, ce.tNombre Estatus, rer.eCodUsuarioEntrega, rer.eCodUsuarioRecoleccion FROM BitEventos be INNER JOIN CatClientes cc ON cc.eCodCliente = be.eCodCliente
															INNER JOIN CatEstatus ce ON ce.eCodEstatus = be.eCodEstatus
                                                            INNER JOIN RelEventosRutas rer ON rer.eCodEvento=be.eCodEvento
														LEFT JOIN SisUsuarios su ON su.eCodUsuario = be.eCodUsuario
                                                        WHERE
                                                        be.fhFechaEvento between $fhFechaInicio AND $fhFechaTermino".
                                                        " AND be.eCodEstatus<>4".
												        ($bAll ? "" : " AND (rer.eCodUsuarioEntrega = ".$_SESSION['sessionAdmin'][0]['eCodUsuario']. " OR rer.eCodUsuarioRecoleccion = ".$_SESSION['sessionAdmin'][0]['eCodUsuario'].")").
														" ORDER BY be.fhFechaEvento ASC";

//echo $select;
														
$rsEventos = mysql_query($select);
?>
<!--estilo-->
<style>
.bodega
    {
        color:#000000;
        background-color: darkgrey;
        padding:5px;
        border-radius: 5px;
    }
    .enlaceBodega
    {
        text-decoration: none;
        color: #666666;
    }
    .enlaceBodega:hover
    {
        text-decoration: none;
        color: #cccccc;
    }
</style>

<!--estilo-->
<div class="row">
<!--calendario-->
    <div class="col-lg-4" >
        <div class="au-card au-card--no-shadow au-card--no-pad m-b-40" id="datepicker" onclick="obtenerFecha()"></div>
        <center>
        <form id="Datos" method="post" action="<?=$_SERVER['PHP_SELF']?>?tCodSeccion=rutas">
    <input type="hidden" name="fhFechaConsulta" id="datepicker1">
    <input type="submit" class="btn btn-info" value="Consultar">
    </form>
        </center>
    </div>
<!--calendario-->
<!--Listado de eventos de ese día-->
<div class="col-lg-8">
          <?
    while($rEvento = mysql_fetch_array($rsEventos))
    { ?>
    <?
        $tAccion = ($rEvento{'eCodUsuarioEntrega'}==$_SESSION['sessionAdmin'][0]['eCodUsuario']) ? 'Entrega' : 'Recoleccion';
        ?>
    <div class="card border border-primary">
                                    <div class="card-header">
                                        <strong class="card-title"><?=$rEvento{'nombreCliente'}?> | <?=date('d/m/Y H:i',strtotime($rEvento{'fhFechaEvento'}))?>
                                            <small>
                                                <span class="badge badge-danger float-right mt-1"><?=$tAccion?></span>
                                            </small>
                                        </strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><?=$rEvento{'tDireccion'}?>
                                        </p>
                                    </div>
                                </div>
    <? } ?>
                            </div>  
<!--Listado de eventos de ese día-->

</div>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.8/angular.min.js"></script>
<script src="https://rawgithub.com/cletourneau/angular-bootstrap-datepicker/master/dist/angular-bootstrap-datepicker.js" charset="utf-8"></script>
<script>



function obtenerFecha()
{
var fecha = $("#datepicker").datepicker( 'getDate' );
var fhFecha = new Date(fecha);

var mes = fhFecha.getMonth();
mes = mes+1;
document.getElementById('datepicker1').value = fhFecha;
/*document.getElementById('datepicker1').value = fhFecha.getDate()+'-'+mes+'-'+fhFecha.getFullYear();*/
   window.location="?tCodSeccion=inicio&fhFechaConsulta="+document.getElementById('datepicker1').value;
}
    
    $('#datepicker').datepicker();
</script>