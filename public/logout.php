<?php
// Archivo: public/logout.php

// Iniciar la sesión para poder acceder a ella.
session_start();

// Destruir todas las variables de sesión.
$_SESSION = [];

// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
// Nota: ¡Esto destruirá la sesión, y no la información de la sesión!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir al login. Usamos una URL absoluta para máxima compatibilidad.
header('Location: login.php');
exit; // Asegurarse de que el script se detiene aquí.
?>