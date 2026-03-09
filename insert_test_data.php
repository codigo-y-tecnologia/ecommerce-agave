<?php

$mysqli = new mysqli('localhost', 'root', '', 'ecommerce_agave');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

echo "=== INSERTANDO DATOS DE PRUEBA ===\n\n";

// 1. Insertar usuarios si no existen
echo "1. Insertando usuarios...\n";
$usuarios = [
    [111, 'Juan', 'Pérez', 'García', 'juan@gmail.com'],
    [222, 'María', 'López', 'Martínez', 'maria@gmail.com'],
    [333, 'Carlos', 'Rodríguez', 'Santos', 'carlos@gmail.com']
];

foreach ($usuarios as $user) {
    $sql = "INSERT IGNORE INTO tbl_usuarios (id_usuario, vNombre, vApaterno, vAmaterno, vEmail, vPassword, eRol) 
            VALUES ({$user[0]}, '{$user[1]}', '{$user[2]}', '{$user[3]}', '{$user[4]}', MD5('123456'), 'cliente')";
    if ($mysqli->query($sql)) {
        echo "   ✓ Usuario {$user[1]} insertado\n";
    }
}

// 2. Insertar direcciones
echo "\n2. Insertando direcciones...\n";
$direcciones = [
    [111, '4531152106', 'Avenida Principal', '123', '', 'Centro', '28001', 'Madrid', 'Madrid', 'Calle 1', 'Calle 2', 'Frente al parque', 1],
    [222, '5551234567', 'Calle Secundaria', '456', 'Apt 101', 'Zona Rosa', '06500', 'Benito Juárez', 'Ciudad de México', 'Calle 3', 'Calle 4', 'Edificio azul', 1],
    [333, '6621234567', 'Avenida del Mar', '789', '', 'Playa', '28920', 'Alcalá de Henares', 'Madrid', 'Calle 5', 'Calle 6', 'Cerca de la playa', 1]
];

foreach ($direcciones as $dir) {
    $sql = "INSERT IGNORE INTO tbl_direcciones (id_usuario, vTelefono_contacto, vCalle, vNumero_exterior, vNumero_interior, vColonia, vCodigo_postal, vCiudad, vEstado, vEntre_calle_1, vEntre_calle_2, tReferencias, bDireccion_principal) 
            VALUES ({$dir[0]}, '{$dir[1]}', '{$dir[2]}', '{$dir[3]}', '{$dir[4]}', '{$dir[5]}', '{$dir[6]}', '{$dir[7]}', '{$dir[8]}', '{$dir[9]}', '{$dir[10]}', '{$dir[11]}', {$dir[12]})";
    if ($mysqli->query($sql)) {
        echo "   ✓ Dirección para usuario {$dir[0]} insertada\n";
    }
}

// 3. Insertar productos
echo "\n3. Insertando productos...\n";
$productos = [
    [1, 'Laptop Dell XPS', '100001', 1200.00, 5],
    [2, 'Mouse Logitech', '100002', 35.00, 20],
    [3, 'Teclado Mecánico RGB', '100003', 150.00, 8],
    [4, 'Monitor LG 4K', '100004', 450.00, 3],
    [5, 'Cable HDMI 2.1', '100005', 25.00, 50]
];

foreach ($productos as $prod) {
    $sql = "INSERT IGNORE INTO tbl_productos (id_producto, vNombre, vCodigo_barras, dPrecio_venta, iStock) 
            VALUES ({$prod[0]}, '{$prod[1]}', '{$prod[2]}', {$prod[3]}, {$prod[4]})";
    if ($mysqli->query($sql)) {
        echo "   ✓ Producto {$prod[1]} insertado\n";
    }
}

// 4. Insertar ventas con los IDs correctos (1-5 para que coincidan con detalle_venta)
echo "\n4. Insertando ventas...\n";
$ventas = [
    [1, 0, 111, '2026-01-20 10:30:00', 1235.00, 'tarjeta', 'completada'],
    [2, 0, 222, '2026-01-21 14:15:00', 485.00, 'transferencia', 'completada'],
    [3, 0, 333, '2026-01-22 09:45:00', 200.00, 'tarjeta', 'completada'],
    [4, 0, 111, '2026-01-23 16:20:00', 600.00, 'stripe', 'completada'],
    [5, 0, 222, '2026-01-24 11:00:00', 475.00, 'tarjeta', 'completada']
];

foreach ($ventas as $venta) {
    $sql = "INSERT IGNORE INTO tbl_ventas (id_venta, id_pedido, id_usuario, tFecha_venta, dTotal, eMetodo_pago, eEstado) 
            VALUES ({$venta[0]}, {$venta[1]}, {$venta[2]}, '{$venta[3]}', {$venta[4]}, '{$venta[5]}', '{$venta[6]}')";
    if ($mysqli->query($sql)) {
        echo "   ✓ Venta {$venta[0]} insertada (Usuario: {$venta[2]}, Total: ${$venta[4]})\n";
    }
}

// 5. Actualizar detalle_venta con los datos correctos
echo "\n5. Actualizando detalles de venta...\n";
$detalles = [
    ['UPDATE tbl_detalle_venta SET id_producto=1, iCantidad=1, dPrecio_unitario=1200.00, dSubtotal=1200.00 WHERE id_detalle_venta=1', 'Detalle 1: Laptop Dell'],
    ['UPDATE tbl_detalle_venta SET id_producto=2, iCantidad=1, dPrecio_unitario=35.00, dSubtotal=35.00 WHERE id_detalle_venta=2', 'Detalle 2: Mouse'],
    ['UPDATE tbl_detalle_venta SET id_producto=3, iCantidad=2, dPrecio_unitario=150.00, dSubtotal=300.00 WHERE id_detalle_venta=3', 'Detalle 3: Teclados'],
    ['UPDATE tbl_detalle_venta SET id_producto=4, iCantidad=1, dPrecio_unitario=450.00, dSubtotal=450.00 WHERE id_detalle_venta=4', 'Detalle 4: Monitor'],
    ['UPDATE tbl_detalle_venta SET id_producto=5, iCantidad=2, dPrecio_unitario=25.00, dSubtotal=50.00 WHERE id_detalle_venta=5', 'Detalle 5: Cables']
];

foreach ($detalles as $detalle) {
    if ($mysqli->query($detalle[0])) {
        echo "   ✓ {$detalle[1]} actualizado\n";
    }
}

echo "\n✅ Datos de prueba insertados exitosamente\n";
echo "\nResumen de datos insertados:\n";
echo "- 3 Usuarios\n";
echo "- 3 Direcciones (principales)\n";
echo "- 5 Productos\n";
echo "- 5 Ventas\n";
echo "- 5 Detalles de Venta\n";

$mysqli->close();
?>
