<?php
/**
 * Punto de arranque de la aplicación.
 * Cada archivo público (public/index.php, public/ver-receta.php, etc.)
 * lo incluye antes de llamar a su controlador.
 */

define('APP_PATH', __DIR__);

require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Session.php';
require_once APP_PATH . '/core/Helpers.php';

// Autoload sencillo: busca la clase primero en models/, luego en controllers/
spl_autoload_register(function ($clase) {
    $rutas = [
        APP_PATH . '/models/' . $clase . '.php',
        APP_PATH . '/controllers/' . $clase . '.php',
    ];

    foreach ($rutas as $ruta) {
        if (file_exists($ruta)) {
            require_once $ruta;
            return;
        }
    }
});

Session::start();
