<?php
require_once __DIR__ . '/../app/bootstrap.php';

// Patrón "Forma 4" 
if (isset($_GET['controller']) && class_exists($_GET['controller'])) {

    $nombreControlador = $_GET['controller'];
    $controlador = new $nombreControlador(); 

    if (isset($_GET['action']) && method_exists($controlador, $_GET['action'])) {
        $accion = $_GET['action'];
        $controlador->$accion();
    } else {
        echo 'Método no existe';
    }

} else {


    $controlador = new HomeController();
    $controlador->index();

}
