<?php
// Incluye el archivo de sesión para poder acceder o destruir la sesión actual
require_once __DIR__ . '/session.php';

// Destruye todos los datos asociados a la sesión actual
// Esto se usa normalmente cuando el usuario cierra sesión
session_destroy();

// Redirige al usuario a la página principal del frontend (inicio)
header("Location: ../frontend/index.php");

// Detiene la ejecución del script para asegurar que la redirección funcione correctamente
exit();
?>
