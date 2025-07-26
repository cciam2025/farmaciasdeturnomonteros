<?php
// ¡¡¡IMPORTANTE!!! Añadir la configuración de la zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

require '../includes/db.php';

// Consulta para obtener la farmacia de turno
$stmt = $pdo->prepare(
    "SELECT f.nombre, t.fecha_actualizacion 
     FROM turno_actual t 
     JOIN farmacias f ON t.farmacia_id = f.id 
     WHERE t.id = 1"
);
$stmt->execute();
$farmacia_de_turno = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ¡¡¡IMPORTANTE!!! Añadir meta tag para que la página se actualice sola cada 5 minutos (300 segundos) -->
    <meta http-equiv="refresh" content="300">
    <title>Farmacia de Turno en Monteros</title>
    <link href="css/style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- He usado la misma fuente que en el dashboard para que se vea igual -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 font-sans flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 text-center">
            
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <span class="text-moss-green">Farmacia de Turno</span> en Monteros
            </h1>
            <p class="text-gray-500 mb-8">Información actualizada en tiempo real</p>

            <!-- El recuadro bonito MEJORADO -->
            <div id="turno-info" class="bg-gradient-to-br from-moss-green to-green-700 text-white p-8 md:p-10 rounded-xl shadow-lg border-2 border-gold-custom/50">
                <?php if ($farmacia_de_turno): ?>
                    <h2 class="text-xl font-light mb-2 opacity-90">Actualmente de turno:</h2>
                    <p class="text-4xl md:text-5xl font-extrabold text-gold-custom tracking-wide uppercase drop-shadow-md animate-pulse">
                        <?php echo htmlspecialchars($farmacia_de_turno['nombre']); ?>
                    </p>
                    <p class="text-xs mt-6 opacity-60">
                        <!-- ¡¡¡CORREGIDO!!! Formato de fecha con la zona horaria correcta -->
                        Actualizado el: <?php echo date("d/m/Y \a \l\a\s H:i", strtotime($farmacia_de_turno['fecha_actualizacion'])); ?> hs.
                    </p>
                <?php else: ?>
                    <h2 class="text-3xl font-bold text-gold-custom">No hay información de turno.</h2>
                    <p class="mt-2 opacity-80">Por favor, intente más tarde.</p>
                <?php endif; ?>
            </div>
            
            <!-- Footer MEJORADO con el enlace -->
            <footer class="mt-8 text-xs text-gray-400">
                <p>by 
                    <a href="https://www.cciam.com.ar" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-600 hover:text-moss-green hover:underline">
                        CCIAM
                    </a>
                </p>
            </footer>

        </div>
    </div>

</body>
</html>