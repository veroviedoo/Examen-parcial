<?php
require("../conexion.php");
$conexion = retornarConexion();


if (isset($_GET['codigofactura']) && is_numeric($_GET['codigofactura'])) {
    $codigofactura = intval($_GET['codigofactura']); // Asegura que el valor sea un entero
    
    
    mysqli_query($conexion, "DELETE FROM facturas WHERE codigo=$codigofactura");
    mysqli_query($conexion, "DELETE FROM detallefactura WHERE codigofactura=$codigofactura");
} else {
    
    die("Error: Código de factura no proporcionado o no válido.");
}


header('Location: /index.php'); 
?>
