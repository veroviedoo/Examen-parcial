<?php
// Importación de librerías y archivos necesarios
require('../../fpdf/fpdf.php');
require("../conexion.php");
$conexion = retornarConexion();

// Verificar que se haya proporcionado el código de la factura en la URL
if (!isset($_GET['codigofactura'])) {
    die("Error: Código de factura no proporcionado");
}

$codigofactura = $_GET['codigofactura'];

// Crear y configurar el PDF
try {
    $fpdf = new FPDF('P', 'mm', 'letter');
    $fpdf->AddPage();
    $fpdf->SetMargins(10, 10, 10);
    
    // Verificar conexión
    if (!$conexion) {
        die("Error: No se pudo conectar a la base de datos");
    }

    // Ejecutar consulta para obtener datos del cliente y fecha
    $datos = mysqli_query($conexion, "SELECT nombre, date_format(fecha,'%d/%m/%Y') as fecha 
                                      FROM facturas AS fact 
                                      JOIN clientes AS cli ON cli.codigo = fact.codigocliente 
                                      WHERE fact.codigo = $codigofactura");

    // Verificar si hay resultados
    if (!$datos || mysqli_num_rows($datos) == 0) {
        die("Error: No se encontraron datos de la factura con el código proporcionado");
    }

    // Obtener datos de la consulta
    $resultado = mysqli_fetch_array($datos);

    // Encabezado del PDF
    $fpdf->SetFont('Arial', 'B', 15);
    $fpdf->SetTextColor(255, 255, 255);
    $fpdf->SetFillColor(116, 92, 151);
    $fpdf->Rect(0, 0, 220, 50, 'F');
    $fpdf->Image('../../img/logo.png', 10, 5);
    $fpdf->SetY(15);
    $fpdf->SetX(100);
    $fpdf->Cell(0, 10, "Factura", 0, 1, 'C');
    $fpdf->SetFont('Arial', '', 12);
    $fpdf->SetY(25);
    $fpdf->SetX(100);
    $fpdf->Cell(0, 10, "Cliente: " . $resultado['nombre'], 0, 1, 'C');
    $fpdf->SetY(35);
    $fpdf->SetX(100);
    $fpdf->Cell(0, 10, "Fecha de emision: " . $resultado['fecha'], 0, 1, 'C');

    // Títulos de la tabla de detalles
    $fpdf->SetY(60);
    $fpdf->SetFont('Arial', 'B', 10);
    $fpdf->SetTextColor(255, 255, 255);
    $fpdf->SetFillColor(79, 78, 77);
    $fpdf->Cell(30, 10, 'Código', 1, 0, 'C', 1);
    $fpdf->Cell(70, 10, 'Descripción', 1, 0, 'C', 1);
    $fpdf->Cell(20, 10, 'Cantidad', 1, 0, 'C', 1);
    $fpdf->Cell(40, 10, 'Precio', 1, 0, 'C', 1);
    $fpdf->Cell(30, 10, 'Total', 1, 1, 'C', 1);

    // Ejecutar consulta para obtener detalles de la factura
    $detalleDatos = mysqli_query($conexion, "SELECT pro.codigo AS codigo, descripcion, round(deta.precio,2) AS precio, 
                                             cantidad, round(deta.precio*cantidad,2) AS preciototal 
                                             FROM detallefactura AS deta 
                                             JOIN productos AS pro ON pro.codigo = deta.codigoproducto 
                                             WHERE codigofactura = $codigofactura");

    // Verificar si hay resultados en detalles
    if (!$detalleDatos || mysqli_num_rows($detalleDatos) == 0) {
        die("Error: No se encontraron detalles de la factura con el código proporcionado");
    }

    // Imprimir cada fila de detalles
    $fpdf->SetFont('Arial', '', 10);
    $fpdf->SetTextColor(0, 0, 0);
    $pagoTotal = 0;
    
    while ($fila = mysqli_fetch_array($detalleDatos)) {
        $fpdf->Cell(30, 10, $fila['codigo'], 1, 0, 'C');
        $fpdf->Cell(70, 10, $fila['descripcion'], 1, 0, 'L');
        $fpdf->Cell(20, 10, $fila['cantidad'], 1, 0, 'C');
        $fpdf->Cell(40, 10, number_format($fila['precio'], 2, ',', '.'), 1, 0, 'R');
        $fpdf->Cell(30, 10, number_format($fila['preciototal'], 2, ',', '.'), 1, 1, 'R');
        $pagoTotal += $fila['preciototal'];
    }

    // Mostrar el total de la factura
    $fpdf->SetFont('Arial', 'B', 12);
    $fpdf->Cell(190, 10, "Importe Total: G. " . number_format($pagoTotal, 0, ',', '.'), 1, 1, 'R');

    // Pie de página
    $fpdf->SetY(-30);
    $fpdf->SetFont('Arial', '', 10);
    $fpdf->SetTextColor(0, 0, 0);
    $fpdf->SetFillColor(116, 92, 151);
    $fpdf->Rect(0, 250, 220, 50, 'F');
    $fpdf->SetY(-15);
    $fpdf->SetX(120);
    $fpdf->Cell(0, 10, 'Gracias por su compra!', 0, 0, 'C');

    // Salida del PDF
    $fpdf->Output('I', 'factura.pdf');

} catch (Exception $e) {
    die("Error al generar el PDF: " . $e->getMessage());
}
?>
