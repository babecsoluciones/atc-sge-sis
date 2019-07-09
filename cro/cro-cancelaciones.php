<?php
require("../cnx/swgc-mysql.php");

date_default_timezone_set('America/Mexico_City');

$hoy = "'".date('Y-m-d H:i:s')."'";

$select = "SELECT eCodEvento, TIMESTAMPDIFF(HOUR,$hoy,fhFechaEvento) Diferencia FROM BitEventos WHERE fhFechaEvento > $hoy and eCodEstatus=1";

echo $select.'<br><br>';

$rsEventos = mysql_query($select);
while($rEvento = mysql_fetch_array($rsEventos))
{
    $eCodEstatus =4;
    if($rEvento{'Diferencia'}<=36)
    {
    $update = "UPDATE BitEventos SET eCodEstatus=4 WHERE eCodEvento = ".$rEvento{'eCodEvento'};
    echo $update.'<br><br>';
    mysql_query($update);
    }
}

?>