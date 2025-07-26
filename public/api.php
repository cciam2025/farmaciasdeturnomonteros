<?php
// Permitir el acceso desde cualquier origen (CORS), 
// crucial para que tu IA pueda consultar la API desde donde sea.
header("Access-Control-Allow-Origin: *");
// Definir que el contenido que devolveremos es de tipo JSON.
header("Content-Type: application/json; charset=UTF-8");

// Incluir nuestra conexión a la base de datos en la nube.
require '../includes/db.php';

// Preparar un array de respuesta por defecto.
$response = [
    'status' => 'error',
    'timestamp' => time(), // Hora actual en formato Unix
    'data' => [
        'farmacia_en_turno' => null,
        'mensaje' => 'No se encontró información de turno disponible en este momento.'
    ]
];

try {
    // Consulta SQL para obtener la farmacia de turno y su nombre.
    // Usamos un JOIN para traer el nombre desde la tabla 'farmacias'.
    $stmt = $pdo->prepare(
        "SELECT f.nombre AS nombre_farmacia, t.fecha_actualizacion 
         FROM turno_actual t 
         JOIN farmacias f ON t.farmacia_id = f.id 
         WHERE t.id = 1"
    );
    $stmt->execute();
    $farmacia_de_turno = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si la consulta encontró un resultado...
    if ($farmacia_de_turno) {
        // ...actualizamos nuestra respuesta con los datos correctos.
        $response['status'] = 'success';
        $response['data'] = [
            'farmacia_en_turno' => $farmacia_de_turno['nombre_farmacia'],
            'actualizado_en' => $farmacia_de_turno['fecha_actualizacion'],
            'mensaje' => 'La farmacia de turno actual es ' . $farmacia_de_turno['nombre_farmacia'] . '.'
        ];
    }

} catch (PDOException $e) {
    // Si hay un error con la base de datos, lo registramos en la respuesta.
    $response['data']['mensaje'] = 'Error en la base de datos: ' . $e->getMessage();
}

// Finalmente, convertimos el array de respuesta a formato JSON y lo "imprimimos".
// JSON_PRETTY_PRINT hace que se vea bonito y ordenado.
echo json_encode($response, JSON_PRETTY_PRINT);

?>