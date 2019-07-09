<?php

include("./cnx/swgc-mysql.php");
require_once("./cls/cls-sistema.php");

$insert = "UPDATE SisSeccionesMenusEmergentes SET tTitulo = 'Agregar transaccion' WHERE tCodPadre = 'cata-ren-con' and ePosicion = 5";


mysql_query($insert);
?>