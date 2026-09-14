<?php
class AuthController
{
    
    public function login(): void
    {
        if (Session::estaLogeado()) {
            header('Location: ' . route('HomeController', 'index'));
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verificar()) {
                $error = 'Token de seguridad inválido. Recarga la página e intenta nuevamente.';
            } else {
                $correo = trim($_POST['correo']);
                $contraseña = $_POST['contraseña'];

                $usuario = Usuario::buscarPorCorreo($correo);

                if ($usuario) {
                    if (password_verify($contraseña, $usuario['contraseña'])) {
                        Session::iniciar((int) $usuario['id'], $usuario['nombre']);
                        header('Location: ' . route('HomeController', 'index'));
                        exit;
                    }
                    $error = 'Contraseña incorrecta';
                } else {
                    $error = 'Usuario no encontrado';
                }
            }
        }

        require APP_PATH . '/views/auth/login.php';
    }

  
    public function register(): void
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verificar()) {
                $error = 'Token de seguridad inválido. Recarga la página e intenta nuevamente.';
            } else {
                $nombre = trim($_POST['nombre']);
                $correo = trim($_POST['correo']);
                $contraseña = $_POST['contraseña'];

                if (Usuario::existeCorreo($correo)) {
                    $error = 'Este correo ya está registrado';
                } elseif (Usuario::crear($nombre, $correo, $contraseña)) {
                    header('Location: ' . route('AuthController', 'login', ['registro' => 'exitoso']));
                    exit;
                } else {
                    $error = 'Error al registrar usuario';
                }
            }
        }

        require APP_PATH . '/views/auth/register.php';
    }

   
    public function logout(): void
    {
        Session::destruir();
        header('Location: ' . route('HomeController', 'index'));
        exit;
    }
}