<?

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("cnx/swgc-mysql.php");

$sql = array();

$sql[] = "INSERT INTO `SisSecciones` (`tCodSeccion`, `tCodPadre`, `tTitulo`, `eCodEstatus`, `ePosicion`, `bFiltro`, `tIcono`) VALUES ('cata-cam-con', 'sis-dash-con', 'Camionetas', 3, 3, 1, 'fa fa-file-text-o'), ('cata-cam-reg', 'cata-cam-con', '+ Camionetas', 3, 1, 0, 'fa fa-file-text-o'), ('cata-car-con', 'sis-dash-con', 'Eventos de Carga', 3, 3, 1, 'fa fa-file-text-o');";

$sql[] = "INSERT INTO `SisSeccionesBotones` (`tCodPadre`, `tCodSeccion`, `tCodBoton`, `tFuncion`, `tEtiqueta`, `ePosicion`) VALUES ('cata-cam-reg', 'cata-cam-reg', 'VA', NULL, NULL, 1), ('cata-cam-reg', 'cata-cam-reg', 'GU', NULL, NULL, 2), ('cata-cam-reg', 'cata-cam-con', 'CO', NULL, NULL, 3), ('cata-cam-con', 'cata-cam-reg', 'NU', NULL, NULL, 1);";

$sql[] = "INSERT INTO `SisSeccionesMenusEmergentes` (`tCodPadre`, `tCodSeccion`, `tCodPermiso`, `tTitulo`, `tAccion`, `tFuncion`, `tValor`, `ePosicion`) VALUES ('cata-cam-con', 'cata-cam-reg', 'A', 'Editar', 'editar', 'window.location=\'url\';', 'editar', 1), ('cata-car-con', 'cata-eve-det', 'A', 'Detalles', 'detalles', 'window.location=\'url\';', 'detalles', 2);";

$sql[] = "INSERT INTO `SisSeccionesReemplazos` (`tBase`, `tNombre`) VALUES ('cam', 'camionetas'), ('car', 'carga');";

for($i=0;$i<sizeof($sql);$i++)
{
    mysql_query($sql[$i]);
}
?>