<?php
class Receta
{
    private const TITULO_MAX = 200;

    // Reglas de los datos: el Modelo las conoce
    public static function validar(array $d): array
    {
        $errores = [];

        if ($d['titulo'] === '') {
            $errores[] = 'El título es obligatorio.';
        } elseif (mb_strlen($d['titulo']) > self::TITULO_MAX) {
            $errores[] = 'El título no puede superar ' . self::TITULO_MAX . ' caracteres.';
        }

        if ($d['descripcion'] === '') {
            $errores[] = 'La descripción es obligatoria.';
        }

        if ($d['ingredientes'] === '') {
            $errores[] = 'Los ingredientes son obligatorios.';
        }

        if ($d['instrucciones'] === '') {
            $errores[] = 'Las instrucciones son obligatorias.';
        }

        if ($d['categoria_id'] <= 0 || !Categoria::buscarPorId($d['categoria_id'])) {
            $errores[] = 'Selecciona una categoría válida.';
        }

        return $errores;
    }

    // Últimas N recetas para la portada
    public static function destacadas(int $limite = 6): array
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare(
            "SELECT r.*, c.nombre as categoria_nombre
             FROM recetas r
             LEFT JOIN categorias c ON r.categoria_id = c.id
             ORDER BY r.fecha_creacion DESC
             LIMIT ?"
        );
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $resultado;
    }

    // Búsqueda con filtros de texto y categoría
    public static function buscar(string $busqueda, int $categoriaId): array
    {
        $conexion = Database::getConexion();

        $sql = "SELECT r.*, c.nombre as categoria_nombre, u.nombre as usuario_nombre
                FROM recetas r
                LEFT JOIN categorias c ON r.categoria_id = c.id
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                WHERE 1=1";

        $params = [];
        $types = '';

        if ($busqueda !== '') {
            $sql .= " AND (r.titulo LIKE ? OR r.descripcion LIKE ? OR r.ingredientes LIKE ?)";
            $term = "%$busqueda%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $types .= 'sss';
        }

        if ($categoriaId > 0) {
            $sql .= " AND r.categoria_id = ?";
            $params[] = $categoriaId;
            $types .= 'i';
        }

        $sql .= " ORDER BY r.fecha_creacion DESC";

        $stmt = $conexion->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $resultado;
    }

    public static function buscarPorId(int $id): ?array
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare(
            "SELECT r.*, c.nombre as categoria_nombre, u.nombre as usuario_nombre
             FROM recetas r
             LEFT JOIN categorias c ON r.categoria_id = c.id
             LEFT JOIN usuarios u ON r.usuario_id = u.id
             WHERE r.id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $receta = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $receta ?: null;
    }

    public static function porUsuario(int $usuarioId): array
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare(
            "SELECT r.*, c.nombre as categoria_nombre
             FROM recetas r
             LEFT JOIN categorias c ON r.categoria_id = c.id
             WHERE r.usuario_id = ?
             ORDER BY r.fecha_creacion DESC"
        );
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $resultado;
    }

    public static function perteneceAUsuario(int $recetaId, int $usuarioId): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare("SELECT id FROM recetas WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $recetaId, $usuarioId);
        $stmt->execute();
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $existe;
    }

    public static function crear(array $datos): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare(
            "INSERT INTO recetas (titulo, descripcion, ingredientes, instrucciones, imagen, categoria_id, usuario_id)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssssii",
            $datos['titulo'],
            $datos['descripcion'],
            $datos['ingredientes'],
            $datos['instrucciones'],
            $datos['imagen'],
            $datos['categoria_id'],
            $datos['usuario_id']
        );
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public static function actualizar(int $id, array $datos): bool
    {
        $conexion = Database::getConexion();
        $stmt = $conexion->prepare(
            "UPDATE recetas
             SET titulo = ?, descripcion = ?, ingredientes = ?, instrucciones = ?, imagen = ?, categoria_id = ?
             WHERE id = ?"
        );
        $stmt->bind_param(
            "sssssii",
            $datos['titulo'],
            $datos['descripcion'],
            $datos['ingredientes'],
            $datos['instrucciones'],
            $datos['imagen'],
            $datos['categoria_id'],
            $id
        );
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    // Elimina la receta y sus dependientes en una transacción: o se borra todo o no se borra nada
    // (requiere tablas InnoDB)
    public static function eliminar(int $id): bool
    {
        $conexion = Database::getConexion();

        $sentencias = [
            "DELETE FROM comentarios WHERE receta_id = ?",
            "DELETE FROM favoritos WHERE receta_id = ?",
            "DELETE FROM recetas WHERE id = ?",
        ];

        $conexion->begin_transaction();

        try {
            foreach ($sentencias as $sql) {
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("i", $id);

                if (!$stmt->execute()) {
                    throw new RuntimeException($stmt->error);
                }

                $stmt->close();
            }

            $conexion->commit();
            return true;
        } catch (Throwable $e) {
            $conexion->rollback();
            error_log('Error al eliminar receta ' . $id . ': ' . $e->getMessage());
            return false;
        }
    }
}