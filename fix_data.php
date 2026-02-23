<?php

$mysqli = new mysqli('localhost', 'root', '', 'ecommerce_agave');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

echo "=== CORRIGIENDO DATOS DE VENTA ===\n\n";

// Primero, limpiar los detalles que no coinciden
echo "1. Eliminando detalles de venta con IDs de venta incorrectos...\n";
$mysqli->query('DELETE FROM tbl_detalle_venta WHERE id_venta IN (1,2,3,4,5,6)');
echo "   ✓ Eliminados\n";

// Ahora insertar detalles que coincidan con las ventas reales
echo "\n2. Insertando detalles correctos para las ventas reales (101, 202, 456)...\n";

$detalles = [
    [1, 101, 1, 1, 1200.00, 1200.00],  // Venta 101 - Laptop
    [2, 101, 2, 1, 35.00, 35.00],      // Venta 101 - Mouse
    [3, 202, 3, 2, 150.00, 300.00],    // Venta 202 - Teclados
    [4, 202, 4, 1, 450.00, 450.00],    // Venta 202 - Monitor
    [5, 456, 5, 2, 25.00, 50.00],      // Venta 456 - Cables
    [6, 456, 1, 1, 1200.00, 1200.00],  // Venta 456 - Laptop extra
];

foreach ($detalles as $detalle) {
    $sql = "INSERT INTO tbl_detalle_venta (id_detalle_venta, id_venta, id_producto, iCantidad, dPrecio_unitario, dSubtotal) 
            VALUES ({$detalle[0]}, {$detalle[1]}, {$detalle[2]}, {$detalle[3]}, {$detalle[4]}, {$detalle[5]})
            ON DUPLICATE KEY UPDATE 
            id_venta = {$detalle[1]}, 
            id_producto = {$detalle[2]}, 
            iCantidad = {$detalle[3]}, 
            dPrecio_unitario = {$detalle[4]}, 
            dSubtotal = {$detalle[5]}";
    
    if ($mysqli->query($sql)) {
        echo "   ✓ Detalle {$detalle[0]}: Venta {$detalle[1]}, Producto {$detalle[2]}\n";
    } else {
        echo "   ❌ Error: " . $mysqli->error . "\n";
    }
}

// Verificar que ahora los JOINs funcionan
echo "\n3. Verificando que los JOINs funcionan...\n";
$sql = "SELECT 
    tbl_detalle_venta.id_detalle_venta,
    tbl_detalle_venta.id_venta,
    tbl_usuarios.vNombre,
    tbl_usuarios.vApaterno,
    tbl_direcciones.vCiudad,
    tbl_productos.vNombre as producto,
    tbl_ventas.dTotal
FROM tbl_detalle_venta
LEFT JOIN tbl_ventas ON tbl_detalle_venta.id_venta = tbl_ventas.id_venta
LEFT JOIN tbl_usuarios ON tbl_ventas.id_usuario = tbl_usuarios.id_usuario
LEFT JOIN tbl_direcciones ON tbl_usuarios.id_usuario = tbl_direcciones.id_usuario
LEFT JOIN tbl_productos ON tbl_detalle_venta.id_producto = tbl_productos.id_producto
ORDER BY tbl_detalle_venta.id_detalle_venta DESC
LIMIT 10";

$result = $mysqli->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "   Detalle {$row['id_detalle_venta']}: {$row['vNombre']} {$row['vApaterno']} - {$row['vCiudad']} - {$row['producto']} - Total: ${$row['dTotal']}\n";
    }
} else {
    echo "   ❌ Error en query: " . $mysqli->error . "\n";
}

echo "\n✅ Datos corregidos\n";

$mysqli->close();
?>
