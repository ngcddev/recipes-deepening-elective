<?php
class Favorito
{
    public static function esFavorito(int $usuarioId, int $recetaId): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("SELECT id FROM favoritos WHERE usuario_id = ? AND receta_id = ?");
        $stmt->bind_param("ii", $usuarioId, $recetaId);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public static function agregar(int $usuarioId, int $recetaId): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("INSERT INTO favoritos (usuario_id, receta_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $usuarioId, $recetaId);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public static function eliminar(int $usuarioId, int $recetaId): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("DELETE FROM favoritos WHERE usuario_id = ? AND receta_id = ?");
        $stmt->bind_param("ii", $usuarioId, $recetaId);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public static function porUsuario(int $usuarioId): array
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare(
            "SELECT r.*, c.nombre as categoria_nombre, u.nombre as usuario_nombre
             FROM recetas r
             LEFT JOIN categorias c ON r.categoria_id = c.id
             LEFT JOIN usuarios u ON r.usuario_id = u.id
             INNER JOIN favoritos f ON r.id = f.receta_id
             WHERE f.usuario_id = ?
             ORDER BY f.fecha DESC"
        );
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $resultado;
    }
}
