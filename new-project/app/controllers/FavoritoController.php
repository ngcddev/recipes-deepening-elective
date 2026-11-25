<?php
class FavoritoController
{
    public function index(): void
    {
        $usuario_id = requerir_login();
        $favoritos  = Favorito::porUsuario($usuario_id);

        require APP_PATH . '/views/favoritos/index.php';
    }

    public function agregar(): void
    {
        $usuario_id = requerir_login();
        requerir_post();

        $receta_id = intval($_POST['receta_id'] ?? 0);

        if ($receta_id > 0
            && Receta::buscarPorId($receta_id)
            && !Favorito::esFavorito($usuario_id, $receta_id)) {
            Favorito::agregar($usuario_id, $receta_id);
        }

        redirigir(route('RecetaController', 'show', ['id' => $receta_id]));
    }

    public function eliminar(): void
    {
        $usuario_id = requerir_login();
        requerir_post();

        $receta_id = intval($_POST['receta_id'] ?? 0);
        Favorito::eliminar($usuario_id, $receta_id);

        // Lista blanca de destinos: nunca rediriges a una URL que venga del formulario
        if (($_POST['origen'] ?? '') === 'favoritos') {
            redirigir(route('FavoritoController', 'index'));
        }

        redirigir(route('RecetaController', 'show', ['id' => $receta_id]));
    }
}