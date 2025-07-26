<?php
session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires'); // <-- HORA CORREGIDA

// Si no está logueado, lo redirigimos al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$usuario_logueado_id = $_SESSION['user_id'];

require '../includes/db.php';

// Actualizar el turno si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['farmacia_id'])) {
    $farmacia_seleccionada_id = $_POST['farmacia_id'];
    
    $sql = "INSERT INTO turno_actual (id, farmacia_id, fecha_actualizacion, actualizado_por_usuario_id) 
            VALUES (1, ?, NOW(), ?)
            ON CONFLICT (id) DO UPDATE 
            SET farmacia_id = EXCLUDED.farmacia_id, 
                fecha_actualizacion = NOW(),
                actualizado_por_usuario_id = EXCLUDED.actualizado_por_usuario_id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$farmacia_seleccionada_id, $usuario_logueado_id]);

    header("Location: dashboard.php?success=1");
    exit;
}

// --- Obtener TODA la información del turno actual ---
$id_farmacia_en_turno = null;
$nombre_farmacia_en_turno = 'Ninguna seleccionada';
$info_actualizacion = 'No hay datos';

$turno_stmt = $pdo->prepare(
    "SELECT 
        t.farmacia_id, 
        f.nombre AS nombre_farmacia, 
        t.fecha_actualizacion,
        u.usuario AS nombre_usuario
     FROM turno_actual t
     JOIN farmacias f ON t.farmacia_id = f.id
     LEFT JOIN usuarios u ON t.actualizado_por_usuario_id = u.id
     WHERE t.id = 1"
);
$turno_stmt->execute();
$turno_actual = $turno_stmt->fetch(PDO::FETCH_ASSOC);

if ($turno_actual) {
    $id_farmacia_en_turno = $turno_actual['farmacia_id'];
    $nombre_farmacia_en_turno = $turno_actual['nombre_farmacia'];
    $fecha = date("d/m/Y \a \l\a\s H:i", strtotime($turno_actual['fecha_actualizacion']));
    $usuario_actualizador = $turno_actual['nombre_usuario'] ? htmlspecialchars($turno_actual['nombre_usuario']) : 'desconocido';
    $info_actualizacion = "Actualizado por <strong>{$usuario_actualizador}</strong> el {$fecha} hs.";
}

// --- OBTENER FARMACIAS Y FILTRAR "ADMINISTRACIÓN" ---
// Traemos todas las farmacias que NO se llamen 'Administración'
$farmacias_stmt = $pdo->query("SELECT id, nombre FROM farmacias WHERE nombre != 'Administración' ORDER BY nombre ASC");
$farmacias = $farmacias_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Turno</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen">
    
    <div class="w-full max-w-3xl mx-auto p-8 bg-white rounded-xl shadow-lg text-center">
        <h1 class="text-3xl font-bold text-moss-green mb-2">Panel de Control de Turnos</h1>
        <p class="text-gray-600 mb-8">Selecciona la farmacia que inicia el turno.</p>

        <div class="mb-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-700">Farmacia de Turno Actual:</h3>
            <p class="text-2xl font-bold text-moss-green">
                <?php echo htmlspecialchars($nombre_farmacia_en_turno); ?>
            </p>
            <p class="text-xs text-gray-500 mt-2"><?php echo $info_actualizacion; ?></p>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">¡Éxito!</p>
                <p>¡Turno actualizado correctamente!</p>
            </div>
        <?php endif; ?>

        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">¿Qué farmacia está de turno?</h2>
            <!-- CORREGIDO: Vuelve la cuadrícula de 3 columnas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($farmacias as $farmacia): ?>
                    <?php
                        $isActive = ($farmacia['id'] == $id_farmacia_en_turno);
                        $buttonClasses = $isActive
                            ? 'bg-moss-green text-white border-gold-custom'
                            : 'bg-white text-moss-green border-moss-green hover:bg-moss-green hover:text-white hover:border-gold-custom';
                    ?>
                    <form method="POST" class="w-full">
                        <input type="hidden" name="farmacia_id" value="<?php echo $farmacia['id']; ?>">
                        <button type="submit" class="w-full border-2 font-semibold py-4 px-2 rounded-lg transition-all duration-300 shadow-sm <?php echo $buttonClasses; ?>">
                            <?php echo htmlspecialchars($farmacia['nombre']); ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="mt-8">
            <a href="logout.php" class="text-sm text-red-500 hover:underline">Cerrar Sesión</a>
             | 
            <a href="index.php" target="_blank" class="text-sm text-moss-green hover:underline">Ver página pública</a>
        </div>
    </div>
</body>
</html>