<?php
require_once("cnx/swgc-mysql.php");


$select = "SELECT * FROM BitEventos";
$rsEventos = mysql_query($select);
while($rEvento = mysql_fetch_array($rsEventos))
{
    $select = "SELECT eCodUsuario FROM CatClientes WHERE eCodCliente = ".$rEvento{'eCodCliente'};
    $rsCliente = mysql_query($select);
    $rCliente = mysql_fetch_array($rsCliente);
    $eCodUsuario = $rCliente{'eCodUsuario'};
    $eCodEvento = $rEvento{'eCodEvento'};
    mysql_query("UPDATE BitEventos SET eCodUsuario = $eCodUsuario WHERE eCodEvento = $eCodEvento");
}

?>
