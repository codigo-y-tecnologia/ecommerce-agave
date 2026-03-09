<?php

$mysqli = new mysqli('localhost', 'root', '', 'ecommerce_agave');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

echo "=== INSERTANDO 2 USUARIOS ADICIONALES ===\n\n";

$usuarios = [
    [444, 'Ana', 'Martínez', 'López', 'ana@gmail.com'],
    [555, 'Roberto', 'García', 'Fernández', 'roberto@gmail.com']
];

foreach ($usuarios as $user) {
    $sql = "INSERT INTO tbl_usuarios (id_usuario, vNombre, vApaterno, vAmaterno, vEmail, vPassword, eRol) 
            VALUES ({$user[0]}, '{$user[1]}', '{$user[2]}', '{$user[3]}', '{$user[4]}', MD5('123456'), 'cliente')";
    
    if ($mysqli->query($sql)) {
        echo "   ✓ Usuario insertado: {$user[1]} {$user[2]} {$user[3]} ({$user[4]})\n";
    } else {
        echo "   ❌ Error: " . $mysqli->error . "\n";
    }
}

echo "\n=== VERIFICANDO TOTAL DE USUARIOS ===\n";
$result = $mysqli->query('SELECT COUNT(*) as total FROM tbl_usuarios');
$row = $result->fetch_assoc();
echo "Total de usuarios registrados: {$row['total']}\n";

echo "\n=== LISTADO DE USUARIOS ===\n";
$result = $mysqli->query('SELECT id_usuario, vNombre, vApaterno, vEmail FROM tbl_usuarios ORDER BY id_usuario');
while ($row = $result->fetch_assoc()) {
    echo "{$row['id_usuario']} - {$row['vNombre']} {$row['vApaterno']} ({$row['vEmail']})\n";
}

$mysqli->close();
?>
