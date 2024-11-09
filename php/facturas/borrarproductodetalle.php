<?php
header('Content-Type: application/json');
require("../conexion.php");

$conexion = retornarConexion();

$respuesta = mysqli_query($conexion, "DELETE FROM detallefactura WHERE codigo=".$_GET['codigo']);
echo json_encode($respuesta);
?>
