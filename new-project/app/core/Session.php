<?php

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function estaLogeado(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    public static function usuarioId(): ?int
    {
        return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
    }

    public static function usuarioNombre(): string
    {
        return $_SESSION['usuario_nombre'] ?? 'Invitado';
    }

    public static function iniciar(int $id, string $nombre): void
    {
        $_SESSION['usuario_id'] = $id;
        $_SESSION['usuario_nombre'] = $nombre;
    }

    public static function destruir(): void
    {
        session_destroy();
    }
}
