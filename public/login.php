<?php
session_start();
require '../includes/db.php';

$error = '';
$debug_info = []; // Array para guardar información de depuración

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password_ingresada = $_POST['password'];

    // 1. Comprobar si el usuario existe en la DB
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // El usuario SÍ existe. Vamos a depurar la contraseña.
        $hash_guardado_en_db = $user['password'];
        
        $debug_info['usuario_encontrado'] = 'Sí, usuario: ' . $user['usuario'];
        $debug_info['password_ingresada'] = 'Contraseña que escribiste: ' . htmlspecialchars($password_ingresada);
        $debug_info['hash_de_la_db'] = 'Hash guardado: ' . htmlspecialchars($hash_guardado_en_db);

        // 2. Verificar la contraseña
        if (password_verify($password_ingresada, $hash_guardado_en_db)) {
            // ¡ÉXITO! La contraseña es correcta.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['farmacia_id'] = $user['farmacia_id'];
            header('Location: dashboard.php');
            exit;
        } else {
            // La contraseña NO es correcta.
            $error = 'Usuario o contraseña incorrectos.';
            $debug_info['verificacion_fallida'] = 'password_verify() devolvió FALSE. Las contraseñas no coinciden.';
        }
    } else {
        // El usuario NO existe.
        $error = 'Usuario o contraseña incorrectos.';
        $debug_info['usuario_encontrado'] = 'No, el usuario "' . htmlspecialchars($usuario) . '" no existe en la base de datos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso para Farmacias</title>
    <!-- Tu HTML del head aquí... -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-moss-green flex flex-col items-center justify-center min-h-screen p-4 font-sans">

    <!-- Tu HTML del título y el form aquí... -->
    <div class="text-center mb-8 text-white">
        <h1 class="text-4xl font-black tracking-tight drop-shadow-lg">Sistema de Farmacias de Turno</h1>
        <h2 class="text-2xl font-light text-gold-custom/80">Monteros</h2>
    </div>
    <div class="w-full max-w-sm mx-auto bg-white/90 backdrop-blur-sm p-8 rounded-2xl shadow-2xl">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Acceso Interno</h2>
        <form method="POST" action="login.php">
            <?php if($error): ?>
                <p class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm"><?php echo $error; ?></p>
            <?php endif; ?>
            <!-- ... tus inputs de usuario y contraseña ... -->
             <div class="mb-4">
                <label for="usuario" class="block text-gray-700 mb-2 font-semibold">Usuario</label>
                <input type="text" id="usuario" name="usuario" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-custom transition-shadow" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 mb-2 font-semibold">Contraseña</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-custom transition-shadow" required>
            </div>
            <button type="submit" class="w-full bg-moss-green text-white py-3 rounded-lg hover:bg-green-800 transition-colors duration-300 font-bold text-lg shadow-md hover:shadow-lg transform hover:scale-105">
                Ingresar
            </button>
        </form>
    </div>
    
    <!-- NUEVO: Caja de Depuración -->
    <?php if (!empty($debug_info)): ?>
    <div class="w-full max-w-sm mx-auto bg-yellow-100 border border-yellow-400 text-yellow-800 p-4 rounded-lg mt-6 text-xs">
        <h4 class="font-bold mb-2">Información de Depuración:</h4>
        <ul class="list-disc list-inside">
            <?php foreach ($debug_info as $key => $value): ?>
                <li><strong><?php echo $key; ?>:</strong> <?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <footer class="mt-8 text-sm text-white/60">
        <p>by <span class="font-bold text-white/80">CCIAM</span></p>
    </footer>

</body>
</html>