<?php
class FavoritoController
{
    
    public function index(): void
    {
        if (!Session::estaLogeado()) {
            header('Location: ' . route('AuthController', 'login'));
            exit;
        }

        $usuario_id = Session::usuarioId();

        if (isset($_POST['eliminar_favorito']) && csrf_verificar()) {
            $receta_id = intval($_POST['eliminar_favorito']);
            Favorito::eliminar($usuario_id, $receta_id);
            header('Location: ' . route('FavoritoController', 'index'));
            exit;
        }

        $favoritos = Favorito::porUsuario($usuario_id);

        require APP_PATH . '/views/favoritos/index.php';
    }
}