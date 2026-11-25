<?php

class Database
{
    private static ?mysqli $conexion = null;

    public static function getConexion(): mysqli
    {
        if (self::$conexion === null) {
            $config = require __DIR__ . '/../config/database.php';

            try {
                $conexion = new mysqli(
                    $config['host'],
                    $config['usuario'],
                    $config['password'],
                    $config['base_datos']
                );
            } catch (mysqli_sql_exception $e) {
                self::fallar($e->getMessage());
            }

            if ($conexion->connect_error) {
                self::fallar($conexion->connect_error);
            }

            $conexion->set_charset('utf8mb4');
            self::$conexion = $conexion;
        }

        return self::$conexion;
    }

    private static function fallar(string $detalle): void
    {
        error_log('Error de conexión a la base de datos: ' . $detalle);
        http_response_code(500);
        exit('No se pudo conectar con la base de datos. Intenta más tarde.');
    }
}