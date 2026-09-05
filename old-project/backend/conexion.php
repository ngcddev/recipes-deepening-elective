<?php
// Datos de conexión a la base de datos
$host = "localhost:3306";   // Servidor y puerto donde está MySQL (3307 en este caso)
$usuario = "root";          // Usuario de MySQL
$password = "";             // Contraseña del usuario (vacía en entorno local)
$base_datos = "recepapp";   // Nombre de la base de datos a la que se va a conectar

// Crear la conexión con el servidor MySQL
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar si ocurrió algún error al conectar
if ($conexion->connect_error) {
    // Si hay un error, se detiene la ejecución y se muestra el mensaje
    die("Error de conexión: " . $conexion->connect_error);
}

// Si llega hasta aquí, la conexión se realizó correctamente
?>
