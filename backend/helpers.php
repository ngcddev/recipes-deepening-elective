<?php
// ------------------------------------------------------------
// Funciones auxiliares para manejar rutas y recursos del sitio
// ------------------------------------------------------------

// Función que genera una URL base del proyecto, añadiendo una ruta opcional
function url($path = '') {
    $base_url = '../frontend';  // Directorio base del proyecto
    
    // Retorna la URL completa concatenando la base con el path recibido
    // Ejemplo: url('img/logo.png') → ../frontend/img/logo.png
    return $base_url . ($path ? '/' . ltrim($path, '/') : '');
}

// Función que genera la ruta hacia archivos estáticos (CSS, JS, imágenes, etc.)
function asset($path) {
    // Concatena la carpeta base 'frontend' con la ruta del archivo
    // Ejemplo: asset('css/estilos.css') → ../frontend/css/estilos.css
    return '../frontend/' . ltrim($path, '/');  
}

// Función que obtiene la URL completa de una imagen según su nombre
function obtenerUrlImagen($nombre_imagen) {
    // Si el nombre de la imagen es el “placeholder.jpg” (imagen por defecto)
    if ($nombre_imagen === 'placeholder.jpg') {
        // Devuelve la ruta al placeholder dentro de la carpeta de imágenes
        return asset('img/placeholder.jpg');
    }
    
    // Si no es el placeholder, devuelve la ruta donde se guardan las imágenes subidas
    // Ejemplo: ../uploads/foto1.jpg
    return '../uploads/' . $nombre_imagen;  // Cambia la ruta si tus imágenes se guardan en otra carpeta
}
?>
