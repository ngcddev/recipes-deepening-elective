<?php
require_once __DIR__ . '/../app/bootstrap.php';

const CONTROLLER_DEFAULT = 'HomeController';
const ACTION_DEFAULT     = 'index';

if (isset($_GET['controller'])) {
    $controller = $_GET['controller'];
} elseif (!isset($_GET['controller']) && !isset($_GET['action'])) {
    $controller = CONTROLLER_DEFAULT;
} else {
    show_error();
    exit();
}

if (preg_match('/^[A-Za-z]+Controller$/', $controller) && class_exists($controller)) {
    $controlador = new $controller();

    
    if (isset($_GET['action']) && is_callable([$controlador, $_GET['action']])) {
        $action = $_GET['action'];
        $controlador->$action();
    } elseif (!isset($_GET['controller']) && !isset($_GET['action'])) {
        $action = ACTION_DEFAULT;
        $controlador->$action();
    } else {
        show_error();
    }
} else {
    show_error();
}