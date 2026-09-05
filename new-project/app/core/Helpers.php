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
