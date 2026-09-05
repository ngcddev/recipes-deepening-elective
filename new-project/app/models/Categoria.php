<?php
class Categoria
{
    public static function todas(): array
    {
        $conexion = Database::getConexion();
        $resultado = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public static function destacadas(int $limite = 4): array
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("SELECT * FROM categorias LIMIT ?");
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $resultado;
    }

    public static function buscarPorId(int $id): ?array
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $categoria = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $categoria ?: null;
    }
}
