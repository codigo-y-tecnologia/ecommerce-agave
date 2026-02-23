<?php

$mysqli = new mysqli('localhost', 'root', '', 'ecommerce_agave');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

echo "=== DETALLE_VENTA ===\n";
$result = $mysqli->query('SELECT id_detalle_venta, id_venta, id_producto FROM tbl_detalle_venta LIMIT 5');
while ($row = $result->fetch_assoc()) {
    echo json_encode($row) . "\n";
}

echo "\n=== VENTAS ===\n";
$result = $mysqli->query('SELECT id_venta, id_usuario, dTotal FROM tbl_ventas LIMIT 5');
while ($row = $result->fetch_assoc()) {
    echo json_encode($row) . "\n";
}

echo "\n=== DIRECCIONES ===\n";
$result = $mysqli->query('SELECT id_usuario, vTelefono_contacto, bDireccion_principal FROM tbl_direcciones LIMIT 5');
while ($row = $result->fetch_assoc()) {
    echo json_encode($row) . "\n";
}

echo "\n=== FULL QUERY TEST ===\n";
$sql = "SELECT 
    tbl_detalle_venta.id_detalle_venta,
    tbl_detalle_venta.id_venta,
    tbl_ventas.id_usuario,
    tbl_usuarios.vNombre,
    tbl_direcciones.vCiudad
FROM tbl_detalle_venta
LEFT JOIN tbl_ventas ON tbl_detalle_venta.id_venta = tbl_ventas.id_venta
LEFT JOIN tbl_usuarios ON tbl_ventas.id_usuario = tbl_usuarios.id_usuario
LEFT JOIN tbl_direcciones ON tbl_ventas.id_usuario = tbl_direcciones.id_usuario AND tbl_direcciones.bDireccion_principal = 1
LIMIT 5";

$result = $mysqli->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "Error: " . $mysqli->error . "\n";
}

$mysqli->close();
?>
