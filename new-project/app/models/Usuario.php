<?php
class Usuario
{
    // Busca un usuario por su correo. Devuelve el array con id, nombre y contraseña (hash), o null.
    public static function buscarPorCorreo(string $correo): ?array
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("SELECT id, nombre, contraseña FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $usuario ?: null;
    }

    public static function existeCorreo(string $correo): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public static function crear(string $nombre, string $correo, string $contraseñaPlano): bool
    {
        $conexion = Database::getConexion();
        $hash = password_hash($contraseñaPlano, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $correo, $hash);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
