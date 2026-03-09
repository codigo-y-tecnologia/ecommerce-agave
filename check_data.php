<?php

$mysqli = new mysqli('localhost', 'root', '', 'ecommerce_agave');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

echo "=== VERIFICANDO DATOS EXISTENTES ===\n\n";

echo "1. USUARIOS:\n";
$result = $mysqli->query('SELECT id_usuario, vNombre, vApaterno, vAmaterno FROM tbl_usuarios');
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "   ID: {$row['id_usuario']} - {$row['vNombre']} {$row['vApaterno']} {$row['vAmaterno']}\n";
    }
} else {
    echo "   ❌ No hay usuarios\n";
}

echo "\n2. DIRECCIONES:\n";
$result = $mysqli->query('SELECT id_usuario, vCiudad, vEstado, vTelefono_contacto FROM tbl_direcciones');
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "   Usuario: {$row['id_usuario']} - {$row['vCiudad']}, {$row['vEstado']} - Tel: {$row['vTelefono_contacto']}\n";
    }
} else {
    echo "   ❌ No hay direcciones\n";
}

echo "\n3. PRODUCTOS:\n";
$result = $mysqli->query('SELECT id_producto, vNombre FROM tbl_productos');
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "   ID: {$row['id_producto']} - {$row['vNombre']}\n";
    }
} else {
    echo "   ❌ No hay productos\n";
}

echo "\n4. VENTAS:\n";
$result = $mysqli->query('SELECT id_venta, id_usuario, dTotal FROM tbl_ventas');
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "   ID: {$row['id_venta']} - Usuario: {$row['id_usuario']} - Total: {$row['dTotal']}\n";
    }
} else {
    echo "   ❌ No hay ventas\n";
}

echo "\n5. DETALLES VENTA:\n";
$result = $mysqli->query('SELECT id_detalle_venta, id_venta, id_producto FROM tbl_detalle_venta');
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "   ID: {$row['id_detalle_venta']} - Venta: {$row['id_venta']} - Producto: {$row['id_producto']}\n";
    }
} else {
    echo "   ❌ No hay detalles de venta\n";
}

$mysqli->close();
?>
