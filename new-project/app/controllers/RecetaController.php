<?php
class RecetaController
{
    public function index(): void
    {
        $busqueda = isset($_GET['busqueda']) && is_string($_GET['busqueda'])
            ? trim($_GET['busqueda'])
            : '';
        $categoria_id = intval($_GET['categoria'] ?? 0);

        $recetas    = Receta::buscar($busqueda, $categoria_id);
        $categorias = Categoria::todas();

        $categoria_nombre = '';
        if ($categoria_id > 0) {
            $categoria = Categoria::buscarPorId($categoria_id);
            $categoria_nombre = $categoria['nombre'] ?? '';
        }

        require APP_PATH . '/views/recetas/index.php';
    }

    public function show(): void
    {
        $receta_id = intval($_GET['id'] ?? 0);
        $receta    = $receta_id > 0 ? Receta::buscarPorId($receta_id) : null;

        if (!$receta) {
            show_error('La receta que buscas no existe.');
            return;
        }

        $usuario_id  = Session::usuarioId();
        $es_favorito = Session::estaLogeado() && Favorito::esFavorito($usuario_id, $receta_id);
        $comentarios = Comentario::porReceta($receta_id);

        require APP_PATH . '/views/recetas/show.php';
    }

    public function mine(): void
    {
        $usuario_id = requerir_login();
        $mensaje    = flash_obtener();
        $recetas    = Receta::porUsuario($usuario_id);

        require APP_PATH . '/views/recetas/mine.php';
    }

    public function create(): void
    {
        $usuario_id = requerir_login();

        $mensaje = '';
        $titulo = $descripcion = $ingredientes = $instrucciones = '';
        $categoria_id = 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->datosFormulario();

            // Se conservan los datos escritos para repintar el formulario si algo falla
            $titulo        = $datos['titulo'];
            $descripcion   = $datos['descripcion'];
            $ingredientes  = $datos['ingredientes'];
            $instrucciones = $datos['instrucciones'];
            $categoria_id  = $datos['categoria_id'];

            if (!csrf_verificar()) {
                $mensaje = 'Error: token de seguridad inválido. Recarga la página e intenta nuevamente.';
            } else {
                $errores = Receta::validar($datos);

                if ($errores) {
                    $mensaje = 'Error: ' . implode(' ', $errores);
                } else {
                    $imagen = Imagen::guardar($_FILES['imagen'] ?? null);

                    if ($imagen['error']) {
                        $mensaje = 'Error: ' . $imagen['error'];
                    } else {
                        $datos['imagen']     = $imagen['nombre'];
                        $datos['usuario_id'] = $usuario_id;

                        if (Receta::crear($datos)) {
                            flash('¡Receta agregada exitosamente!');
                            redirigir(route('RecetaController', 'mine'));
                        }

                        Imagen::eliminar($imagen['nombre']); // no dejar archivos huérfanos
                        $mensaje = 'Error: no se pudo agregar la receta. Intenta nuevamente.';
                    }
                }
            }
        }

        $categorias = Categoria::todas();

        require APP_PATH . '/views/recetas/create.php';
    }

    public function edit(): void
    {
        $usuario_id = requerir_login();
        $mensaje    = '';

        $receta_id = intval($_GET['id'] ?? 0);
        $receta    = $receta_id > 0 ? Receta::buscarPorId($receta_id) : null;

        if (!$receta || !Receta::perteneceAUsuario($receta_id, $usuario_id)) {
            redirigir(route('RecetaController', 'mine'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->datosFormulario();

            if (!csrf_verificar()) {
                $mensaje = 'Error: token de seguridad inválido. Recarga la página e intenta nuevamente.';
            } else {
                $errores = Receta::validar($datos);

                if ($errores) {
                    $mensaje = 'Error: ' . implode(' ', $errores);
                    $receta  = array_merge($receta, $datos);
                } else {
                    $imagen = Imagen::guardar($_FILES['imagen'] ?? null, $receta['imagen']);

                    if ($imagen['error']) {
                        $mensaje = 'Error: ' . $imagen['error'];
                        $receta  = array_merge($receta, $datos);
                    } else {
                        $datos['imagen'] = $imagen['nombre'];
                        $imagen_nueva    = $imagen['nombre'] !== $receta['imagen'];

                        if (Receta::actualizar($receta_id, $datos)) {
                            if ($imagen_nueva) {
                                Imagen::eliminar($receta['imagen']); // la anterior ya no se usa
                            }
                            flash('¡Receta actualizada exitosamente!');
                            redirigir(route('RecetaController', 'mine'));
                        }

                        if ($imagen_nueva) {
                            Imagen::eliminar($imagen['nombre']); // la actualización falló
                        }
                        $mensaje = 'Error: no se pudo actualizar la receta. Intenta nuevamente.';
                        unset($datos['imagen']);
                        $receta = array_merge($receta, $datos);
                    }
                }
            }
        }

        $categorias = Categoria::todas();

        require APP_PATH . '/views/recetas/edit.php';
    }

    public function eliminar(): void
    {
        $usuario_id = requerir_login();
        requerir_post();

        $receta_id = intval($_POST['receta_id'] ?? 0);
        $receta    = $receta_id > 0 ? Receta::buscarPorId($receta_id) : null;

        if (!$receta) {
            flash('Error: la receta no existe.');
        } elseif (!Receta::perteneceAUsuario($receta_id, $usuario_id)) {
            flash('Error: no tienes permisos para eliminar esta receta.');
        } elseif (Receta::eliminar($receta_id)) {
            Imagen::eliminar($receta['imagen']);
            flash('Receta eliminada exitosamente.');
        } else {
            flash('Error: no se pudo eliminar la receta.');
        }

        redirigir(route('RecetaController', 'mine'));
    }

    // Lee y normaliza los campos del formulario (sin avisos si falta alguno)
    private function datosFormulario(): array
    {
        return [
            'titulo'        => trim($_POST['titulo'] ?? ''),
            'descripcion'   => trim($_POST['descripcion'] ?? ''),
            'ingredientes'  => trim($_POST['ingredientes'] ?? ''),
            'instrucciones' => trim($_POST['instrucciones'] ?? ''),
            'categoria_id'  => intval($_POST['categoria_id'] ?? 0),
        ];
    }
}