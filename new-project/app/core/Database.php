<?php

class Database
{
    private static ?mysqli $conexion = null;

    public static function getConexion(): mysqli
    {
        if (self::$conexion === null) {
            $config = require __DIR__ . '/../config/database.php';

            self::$conexion = new mysqli(
                $config['host'],
                $config['usuario'],
                $config['password'],
                $config['base_datos']
            );

            if (self::$conexion->connect_error) {
                die('Error de conexión: ' . self::$conexion->connect_error);
            }

            self::$conexion->set_charset('utf8mb4');
        }

        return self::$conexion;
    }
}
