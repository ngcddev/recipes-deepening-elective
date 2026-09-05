<?php
class AuthController
{
    // GET/POST /iniciar-sesion.php
    public function login(): void
    {
        if (Session::estaLogeado()) {
            header('Location: ' . url('index.php'));
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo']);
            $contraseña = $_POST['contraseña'];

            $usuario = Usuario::buscarPorCorreo($correo);

            if ($usuario) {
                if (password_verify($contraseña, $usuario['contraseña'])) {
                    Session::iniciar((int) $usuario['id'], $usuario['nombre']);
                    header('Location: ' . url('index.php'));
                    exit;
                }
                $error = 'Contraseña incorrecta';
            } else {
                $error = 'Usuario no encontrado';
            }
        }

        require APP_PATH . '/views/auth/login.php';
    }

    // GET/POST /registrarse.php
    public function register(): void
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $correo = trim($_POST['correo']);
            $contraseña = $_POST['contraseña'];

            if (Usuario::existeCorreo($correo)) {
                $error = 'Este correo ya está registrado';
            } elseif (Usuario::crear($nombre, $correo, $contraseña)) {
                header('Location: ' . url('iniciar-sesion.php?registro=exitoso'));
                exit;
            } else {
                $error = 'Error al registrar usuario';
            }
        }

        require APP_PATH . '/views/auth/register.php';
    }

    // GET /logout.php
    public function logout(): void
    {
        Session::destruir();
        header('Location: ' . url('index.php'));
        exit;
    }
}
