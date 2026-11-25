<?php
class ComentarioController
{
    private const MAX_CARACTERES = 1000; 

    public function crear(): void
    {
        $usuario_id = requerir_login();
        requerir_post();

        $receta_id = intval($_POST['receta_id'] ?? 0);
        $texto     = trim($_POST['comentario'] ?? '');

        if ($receta_id > 0
            && $texto !== ''
            && mb_strlen($texto) <= self::MAX_CARACTERES
            && Receta::buscarPorId($receta_id)) {
            Comentario::crear($usuario_id, $receta_id, $texto);
        }

        redirigir(route('RecetaController', 'show', ['id' => $receta_id]));
    }

    public function eliminar(): void
    {
        $usuario_id = requerir_login();
        requerir_post();

        $comentario_id = intval($_POST['comentario_id'] ?? 0);
        $receta_id     = intval($_POST['receta_id'] ?? 0);

        Comentario::eliminarDeUsuario($comentario_id, $usuario_id);

        redirigir(route('RecetaController', 'show', ['id' => $receta_id]));
    }
}