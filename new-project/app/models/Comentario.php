<?php
class Comentario
{
    public static function porReceta(int $recetaId): array
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare(
            "SELECT c.*, u.nombre as usuario_nombre
             FROM comentarios c
             LEFT JOIN usuarios u ON c.usuario_id = u.id
             WHERE c.receta_id = ?
             ORDER BY c.fecha DESC"
        );
        $stmt->bind_param("i", $recetaId);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $resultado;
    }

    public static function crear(int $usuarioId, int $recetaId, string $texto): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("INSERT INTO comentarios (usuario_id, receta_id, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $usuarioId, $recetaId, $texto);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    // Verifica la propiedad y borra en una sola consulta
    public static function eliminarDeUsuario(int $comentarioId, int $usuarioId): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("DELETE FROM comentarios WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $comentarioId, $usuarioId);
        $stmt->execute();
        $borrado = $stmt->affected_rows > 0;
        $stmt->close();

        return $borrado;
    }
}