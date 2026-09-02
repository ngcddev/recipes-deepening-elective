<?php
// Inicia la sesión o reanuda una existente.
// Es necesario para poder usar la variable superglobal $_SESSION
session_start();

// ------------------------------------------------------------
// Funciones relacionadas con la sesión del usuario
// ------------------------------------------------------------

// Verifica si el usuario ha iniciado sesión
function usuarioEstaLogeado() {
    // Devuelve true si existe una variable de sesión llamada 'usuario_id'
    // De lo contrario, devuelve false
    return isset($_SESSION['usuario_id']);
}

// Obtiene el ID del usuario actualmente logueado
function obtenerUsuarioId() {
    // Retorna el valor almacenado en 'usuario_id' o null si no existe
    return $_SESSION['usuario_id'] ?? null;
}

// Obtiene el nombre del usuario actualmente logueado
function obtenerUsuarioNombre() {
    // Retorna el nombre guardado en 'usuario_nombre'
    // Si no existe, devuelve el texto 'Invitado' por defecto
    return $_SESSION['usuario_nombre'] ?? 'Invitado';
}
?>
