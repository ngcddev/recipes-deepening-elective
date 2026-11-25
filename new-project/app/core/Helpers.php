<?php

function base_url(): string
{
    static $base = null;

    if ($base === null) {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    }

    return $base;
}

function url(string $path = ''): string
{
    return base_url() . ($path ? '/' . ltrim($path, '/') : '/');
}

function asset(string $path): string
{
    return base_url() . '/' . ltrim($path, '/');
}

function obtenerUrlImagen(?string $nombre_imagen): string
{
    if (empty($nombre_imagen) || $nombre_imagen === 'placeholder.jpg') {
        return asset('img/placeholder.jpg');
    }

    return asset('img/' . $nombre_imagen);
}

function route(string $controlador, string $accion, array $params = []): string
{
    $query = array_merge(['controller' => $controlador, 'action' => $accion], $params);

    return url('index.php') . '?' . http_build_query($query);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verificar(): bool
{
    $token = $_POST['csrf_token'] ?? '';

    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function show_error(string $mensaje = 'La página que buscas no existe.', int $codigo = 404): void
{
    http_response_code($codigo);
    $titulo_pagina = 'Error ' . $codigo . ' - RecepApp';
    require APP_PATH . '/views/error.php';
}

/*  Helpers de flujo (controladores)  */

function redirigir(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// Exige sesión iniciada y devuelve el id del usuario
function requerir_login(): int
{
    if (!Session::estaLogeado()) {
        redirigir(route('AuthController', 'login'));
    }

    return (int) Session::usuarioId();
}

// Exige método POST y token CSRF válido
function requerir_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verificar()) {
        show_error('Solicitud no válida.', 400);
        exit;
    }
}

// Mensaje que sobrevive a una redirección (patrón Post/Redirect/Get)
function flash(string $mensaje): void
{
    $_SESSION['flash'] = $mensaje;
}

function flash_obtener(): string
{
    $mensaje = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);

    return $mensaje;
}