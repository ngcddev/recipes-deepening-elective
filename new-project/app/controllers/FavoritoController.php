<?php
class FavoritoController
{
    // GET/POST /ver-favoritos.php
    public function index(): void
    {
        if (!Session::estaLogeado()) {
            header('Location: ' . url('iniciar-sesion.php'));
            exit;
        }

        $usuario_id = Session::usuarioId();

        if (isset($_POST['eliminar_favorito'])) {
            $receta_id = intval($_POST['eliminar_favorito']);
            Favorito::eliminar($usuario_id, $receta_id);
            header('Location: ' . url('ver-favoritos.php'));
            exit;
        }

        $favoritos = Favorito::porUsuario($usuario_id);

        require APP_PATH . '/views/favoritos/index.php';
    }
}
