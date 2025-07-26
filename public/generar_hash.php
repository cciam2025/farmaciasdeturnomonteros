<?php
// Este script genera el código SQL para insertar todas las farmacias y usuarios con contraseñas hasheadas correctamente.

$usuarios_a_crear = [
    ['nombre_farmacia' => 'Administración', 'usuario' => 'gonzalonorry', 'password_plana' => '37728712'],
    ['nombre_farmacia' => 'Farmacia Santa Rita', 'usuario' => 'farmaciasantarita', 'password_plana' => 'farmaciasantarita2025'],
    ['nombre_farmacia' => 'Farmacia Monteros', 'usuario' => 'farmaciamonteros', 'password_plana' => 'farmaciamonteros2025'],
    ['nombre_farmacia' => 'Farmacia La Providencia', 'usuario' => 'farmacialaprovidencia', 'password_plana' => 'farmacialaprovidencia2025'],
    ['nombre_farmacia' => 'Farmacia Pakará', 'usuario' => 'farmaciapakara', 'password_plana' => 'farmaciapakara2025'],
    ['nombre_farmacia' => 'Farmacia San Carlos', 'usuario' => 'farmaciasancarlos', 'password_plana' => 'farmaciasancarlos2025'],
    ['nombre_farmacia' => 'Farmacia Ñuñorco', 'usuario' => 'farmaciañuñorco', 'password_plana' => 'farmaciañuñorco2025'],
    ['nombre_farmacia' => 'Farmacia Yurina', 'usuario' => 'farmaciayurina', 'password_plana' => 'farmaciayurina2025'],
    ['nombre_farmacia' => 'Farmacia Colon', 'usuario' => 'farmaciacolon', 'password_plana' => 'farmaciacolon2025'],
    ['nombre_farmacia' => 'Farmacia del Milagro', 'usuario' => 'farmaciadelmilagro', 'password_plana' => 'farmaciadelmilagro2025'],
];

// Encabezado del script SQL
$sql_output = "-- ========================================================\n";
$sql_output .= "-- SCRIPT SQL AUTO-GENERADO PARA POBLAR LA BASE DE DATOS\n";
$sql_output .= "-- ========================================================\n\n";

// PASO 1: Insertar todas las farmacias
$sql_output .= "-- PASO 1: Insertar todas las farmacias\n";
$sql_output .= "INSERT INTO farmacias (nombre) VALUES\n";
$farmacias_values = [];
foreach ($usuarios_a_crear as $data) {
    $farmacias_values[] = "('" . pg_escape_string($data['nombre_farmacia']) . "')";
}
$sql_output .= implode(",\n", $farmacias_values) . "\nON CONFLICT (nombre) DO NOTHING;\n\n";


// PASO 2: Insertar todos los usuarios con contraseñas hasheadas
$sql_output .= "-- PASO 2: Insertar/Actualizar todos los usuarios con hashes reales\n";
foreach ($usuarios_a_crear as $data) {
    $hash = password_hash($data['password_plana'], PASSWORD_DEFAULT);
    $sql_output .= "INSERT INTO usuarios (farmacia_id, usuario, \"password\") VALUES\n";
    $sql_output .= "((SELECT id FROM farmacias WHERE nombre = '" . pg_escape_string($data['nombre_farmacia']) . "'), '" . pg_escape_string($data['usuario']) . "', '" . $hash . "')\n";
    $sql_output .= "ON CONFLICT (usuario) DO UPDATE SET \"password\" = EXCLUDED.\"password\";\n\n";
}

// Mostrar el resultado en un textarea para copiar fácil
echo "<h1>Script SQL Generado</h1>";
echo "<p>Copia todo el contenido de la caja de texto y ejecútalo en el Query Tool de pgAdmin.</p>";
echo "<textarea rows='30' cols='120' readonly>" . htmlspecialchars($sql_output) . "</textarea>";

?>