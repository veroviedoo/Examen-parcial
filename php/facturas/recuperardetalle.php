<?php
require("../conexion.php");

$conexion = retornarConexion();

$datos = mysqli_query($conexion, "SELECT pro.codigo AS codigo,
                                          descripcion,
                                          ROUND(deta.precio,2) AS precio,
                                          cantidad,
                                          ROUND(deta.precio * cantidad, 2) AS preciototal,
                                          deta.codigo AS coddetalle
                                     FROM detallefactura AS deta
                                     JOIN productos AS pro ON pro.codigo = deta.codigoproducto
                                    WHERE codigofactura = $_GET[codigofactura]") 
         OR die(mysqli_error($conexion));

$resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
$pago = 0;
foreach ($resultado as $fila) {
    echo "<tr>";
    echo "<td>$fila[codigo]</td>";
    echo "<td>$fila[descripcion]</td>";
    echo "<td class=\"text-right\">$fila[cantidad]</td>";
    echo "<td class=\"text-right\">$fila[precio]</td>";
    echo "<td class=\"text-right\">$fila[preciototal]</td>";
    echo '<td class="text-right"><a class="btn btn-danger" onclick="borrarItem('.$fila['coddetalle'].')" role="button" href="#" id="'.$fila['coddetalle'].'">Borrar</a></td>';
    echo "</tr>";
    $pago += $fila['preciototal'];
}
echo "<tr>";
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
echo "<td class=\"text-right\"><strong>Importe total</strong></td>";
echo "<td class=\"text-right\"><strong>".number_format(round($pago,2),0,'.','')."</strong></td>";
echo "<td></td>";
echo "</tr>";
?>
