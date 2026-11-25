<?php

class Imagen
{
    private const EXTENSIONES = ['jpg', 'jpeg', 'png', 'gif'];
    private const MIMES       = ['image/jpeg', 'image/png', 'image/gif'];
    private const MAX_BYTES   = 2 * 1024 * 1024; // 2 MB

    private static function directorio(): string
    {
        return dirname(__DIR__, 2) . '/public/img';
    }

    /**
     * Guarda la imagen subida.

     */
    public static function guardar(?array $archivo, ?string $anterior = null): array
    {
        $codigo = $archivo['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($codigo === UPLOAD_ERR_NO_FILE) {
            return $anterior !== null
                ? ['error' => null, 'nombre' => $anterior]
                : ['error' => 'Debes subir una imagen para la receta.', 'nombre' => null];
        }

        if ($codigo !== UPLOAD_ERR_OK) {
            return ['error' => 'No se pudo subir la imagen. Intenta nuevamente.', 'nombre' => $anterior];
        }

        if ($archivo['size'] > self::MAX_BYTES) {
            return ['error' => 'La imagen no puede superar 2 MB.', 'nombre' => $anterior];
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $mime      = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);

        if (!in_array($extension, self::EXTENSIONES, true) || !in_array($mime, self::MIMES, true)) {
            return ['error' => 'Solo se permiten imágenes JPG, PNG o GIF.', 'nombre' => $anterior];
        }

        $directorio = self::directorio();
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $nombre = uniqid('receta_') . '.' . $extension;

        if (!move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombre)) {
            return ['error' => 'No se pudo subir la imagen. Intenta nuevamente.', 'nombre' => $anterior];
        }

        return ['error' => null, 'nombre' => $nombre];
    }

    // Borra una imagen subida por la app 
    public static function eliminar(?string $nombre): void
    {
        if (empty($nombre)) {
            return;
        }

        $nombre = basename($nombre);

        if (strpos($nombre, 'receta_') !== 0) {
            return;
        }

        $ruta = self::directorio() . '/' . $nombre;

        if (is_file($ruta)) {
            unlink($ruta);
        }
    }
}