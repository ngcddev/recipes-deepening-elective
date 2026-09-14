<?php
class RecetaController
{
   
    public function index(): void
    {
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;

        $recetas = Receta::buscar($busqueda, $categoria_id);
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
        if (empty($_GET['id'])) {
            header('Location: ' . route('HomeController', 'index'));
            exit;
        }

        $receta_id = intval($_GET['id']);
        $receta = Receta::buscarPorId($receta_id);

        if (!$receta) {
            header('Location: ' . route('HomeController', 'index'));
            exit;
        }

        $usuario_id = Session::usuarioId();
        $es_favorito = Session::estaLogeado() && Favorito::esFavorito($usuario_id, $receta_id);

      
        if (isset($_POST['accion_favorito']) && Session::estaLogeado() && csrf_verificar()) {
            if ($_POST['accion_favorito'] === 'agregar') {
                Favorito::agregar($usuario_id, $receta_id);
            } elseif ($_POST['accion_favorito'] === 'eliminar') {
                Favorito::eliminar($usuario_id, $receta_id);
            }
            header('Location: ' . route('RecetaController', 'show', ['id' => $receta_id]));
            exit;
        }

     
        if (isset($_POST['comentario']) && Session::estaLogeado() && csrf_verificar()) {
            $texto = trim($_POST['comentario']);
            if ($texto !== '') {
                Comentario::crear($usuario_id, $receta_id, $texto);
            }
            header('Location: ' . route('RecetaController', 'show', ['id' => $receta_id]));
            exit;
        }

       
        if (isset($_POST['eliminar_comentario']) && Session::estaLogeado() && csrf_verificar()) {
            $comentario_id = intval($_POST['eliminar_comentario']);
            if (Comentario::perteneceAUsuario($comentario_id, $usuario_id)) {
                Comentario::eliminar($comentario_id);
            }
            header('Location: ' . route('RecetaController', 'show', ['id' => $receta_id]));
            exit;
        }

        $comentarios = Comentario::porReceta($receta_id);

        require APP_PATH . '/views/recetas/show.php';
    }


    public function mine(): void
    {
        if (!Session::estaLogeado()) {
            header('Location: ' . route('AuthController', 'login'));
            exit;
        }

        $usuario_id = Session::usuarioId();
        $mensaje = '';

        if (isset($_POST['eliminar_receta']) && csrf_verificar()) {
            $receta_id = intval($_POST['eliminar_receta']);

            if (Receta::perteneceAUsuario($receta_id, $usuario_id)) {
                $mensaje = Receta::eliminar($receta_id)
                    ? 'Receta eliminada exitosamente'
                    : ' Error al eliminar la receta';
            } else {
                $mensaje = ' No tienes permisos para eliminar esta receta';
            }
        }

        $recetas = Receta::porUsuario($usuario_id);

        require APP_PATH . '/views/recetas/mine.php';
    }


    public function create(): void
    {
        if (!Session::estaLogeado()) {
            header('Location: ' . route('AuthController', 'login'));
            exit;
        }

        $usuario_id = Session::usuarioId();
        $mensaje = '';
        $titulo = $descripcion = $ingredientes = $instrucciones = '';
        $categoria_id = 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verificar()) {
                $mensaje = 'Token de seguridad inválido. Recarga la página e intenta nuevamente.';
            } else {
                $titulo = trim($_POST['titulo']);
                $descripcion = trim($_POST['descripcion']);
                $ingredientes = trim($_POST['ingredientes']);
                $instrucciones = trim($_POST['instrucciones']);
                $categoria_id = intval($_POST['categoria_id']);

                
                $resultado = $this->procesarImagen($_FILES['imagen'] ?? null, null);

                if ($resultado['error']) {
                    $mensaje = $resultado['error'];
                } else {
                    $ok = Receta::crear([
                        'titulo'        => $titulo,
                        'descripcion'   => $descripcion,
                        'ingredientes'  => $ingredientes,
                        'instrucciones' => $instrucciones,
                        'imagen'        => $resultado['nombre_imagen'],
                        'categoria_id'  => $categoria_id,
                        'usuario_id'    => $usuario_id,
                    ]);

                    if ($ok) {
                        $mensaje = '¡Receta agregada exitosamente!';
                        $titulo = $descripcion = $ingredientes = $instrucciones = '';
                        $categoria_id = 0;
                    } else {
                        $mensaje = 'Error al agregar la receta. Intenta nuevamente.';
                    }
                }
            }
        }

        $categorias = Categoria::todas();

        require APP_PATH . '/views/recetas/create.php';
    }

  
    public function edit(): void
    {
        if (!Session::estaLogeado()) {
            header('Location: ' . route('AuthController', 'login'));
            exit;
        }

        $usuario_id = Session::usuarioId();
        $mensaje = '';

        if (empty($_GET['id'])) {
            header('Location: ' . route('RecetaController', 'mine'));
            exit;
        }

        $receta_id = intval($_GET['id']);
        $receta = Receta::buscarPorId($receta_id);

  
        if (!$receta || (int) $receta['usuario_id'] !== $usuario_id) {
            header('Location: ' . route('RecetaController', 'mine'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verificar()) {
                $mensaje = 'Token de seguridad inválido. Recarga la página e intenta nuevamente.';
            } else {
                $titulo = trim($_POST['titulo']);
                $descripcion = trim($_POST['descripcion']);
                $ingredientes = trim($_POST['ingredientes']);
                $instrucciones = trim($_POST['instrucciones']);
                $categoria_id = intval($_POST['categoria_id']);

                $resultado = $this->procesarImagen($_FILES['imagen'] ?? null, $receta['imagen']);
                $nombre_imagen = $resultado['nombre_imagen'];

                if ($resultado['error']) {
                    $mensaje = $resultado['error'];
                }

                $ok = Receta::actualizar($receta_id, [
                    'titulo'        => $titulo,
                    'descripcion'   => $descripcion,
                    'ingredientes'  => $ingredientes,
                    'instrucciones' => $instrucciones,
                    'imagen'        => $nombre_imagen,
                    'categoria_id'  => $categoria_id,
                ]);

                if ($ok) {
                    $mensaje = $mensaje ?: '¡Receta actualizada exitosamente!';
                    $receta = array_merge($receta, [
                        'titulo'        => $titulo,
                        'descripcion'   => $descripcion,
                        'ingredientes'  => $ingredientes,
                        'instrucciones' => $instrucciones,
                        'categoria_id'  => $categoria_id,
                        'imagen'        => $nombre_imagen,
                    ]);
                } else {
                    $mensaje = 'Error al actualizar la receta. Intenta nuevamente.';
                }
            }
        }

        $categorias = Categoria::todas();

        require APP_PATH . '/views/recetas/edit.php';
    }


    private function procesarImagen(?array $archivo, ?string $imagenAnterior): array
    {
        $imgDir = dirname(__DIR__, 2) . '/public/img';

        $hayArchivoValido = $archivo && ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;


        if (!$hayArchivoValido && $imagenAnterior !== null) {
            return ['error' => null, 'nombre_imagen' => $imagenAnterior];
        }

       
        if (!$hayArchivoValido && $imagenAnterior === null) {
            return ['error' => 'Error: Debes subir una imagen para la receta.', 'nombre_imagen' => null];
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extension, $permitidas, true)) {
            return [
                'error' => 'Error: Solo se permiten imágenes JPG, JPEG, PNG o GIF.',
                'nombre_imagen' => $imagenAnterior,
            ];
        }

        $nombreImagen = uniqid('receta_') . '.' . $extension;
        $rutaDestino = $imgDir . '/' . $nombreImagen;

        if (!is_dir($imgDir)) {
            mkdir($imgDir, 0755, true);
        }

        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return [
                'error' => 'Error: No se pudo subir la imagen. Intenta nuevamente.',
                'nombre_imagen' => $imagenAnterior,
            ];
        }

     
        if ($imagenAnterior && $imagenAnterior !== 'placeholder.jpg') {
            $rutaAnterior = $imgDir . '/' . $imagenAnterior;
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }

        return ['error' => null, 'nombre_imagen' => $nombreImagen];
    }
}