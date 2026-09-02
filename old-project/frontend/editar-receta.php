<?php
// Importa los archivos necesarios para la sesión, conexión y funciones auxiliares
require_once __DIR__ . '/../backend/session.php';
require_once __DIR__ . '/../backend/conexion.php';
require_once '../backend/helpers.php';

// Verificar que el usuario esté loggeado
if (!usuarioEstaLogeado()) {
    // Si el usuario no está logueado, se redirige a la página de inicio de sesión
    header('Location: iniciar-sesion.php');
    exit; // Detiene la ejecución del script
}

// Obtiene el ID del usuario que inició sesión
$usuario_id = $_SESSION['usuario_id'];
// Variable para almacenar mensajes de éxito o error
$mensaje = '';

// Verifica que el parámetro 'id' esté presente en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Si no se pasa un ID, redirige a la lista de recetas propias
    header('Location: ver-recetas-propias.php');
    exit;
}

// Convierte el ID recibido por GET a entero
$receta_id = intval($_GET['id']);

// Verificar que la receta realmente pertenece al usuario logueado
$stmt = $conexion->prepare("SELECT * FROM recetas WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $receta_id, $usuario_id);
$stmt->execute();
// Obtiene los datos de la receta
$receta = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si la receta no existe o no pertenece al usuario, redirige
if (!$receta) {
    header('Location: ver-recetas-propias.php');
    exit;
}

// Procesar el formulario de edición si se envía por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se obtienen los campos enviados y se eliminan espacios en blanco
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $ingredientes = trim($_POST['ingredientes']);
    $instrucciones = trim($_POST['instrucciones']);
    $categoria_id = intval($_POST['categoria_id']);
    
    // Se mantiene la imagen actual por defecto
    $nombre_imagen = $receta['imagen'];
    
    // Procesar nueva imagen solo si el usuario subió una (COPIADO DE AGREGAR RECETA)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        // Obtiene la extensión del archivo
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        // Extensiones válidas permitidas
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Verifica si la extensión es válida
        if (in_array(strtolower($extension), $extensiones_permitidas)) {
            // Genera un nombre único para la imagen (igual que en agregar receta)
            $nombre_imagen = uniqid('receta_') . '.' . $extension;
            // Define la ruta donde se guardará la imagen
            $ruta_destino = __DIR__ . '/img/' . $nombre_imagen;
            
            // Verifica si existe el directorio de imágenes (igual que en agregar receta)
            $img_dir = __DIR__ . '/img';
            if (!is_dir($img_dir)) {
                // Si no existe, lo crea con permisos 755
                mkdir($img_dir, 0755, true);
            }
            
            // Mueve la imagen subida a la carpeta destino
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                // Si se subió correctamente, elimina la imagen anterior (si existe y no es placeholder)
                if (!empty($receta['imagen']) && $receta['imagen'] !== 'placeholder.jpg') {
                    $ruta_anterior = $img_dir . '/' . $receta['imagen'];
                    // Verifica si el archivo existe antes de eliminarlo
                    if (file_exists($ruta_anterior)) {
                        unlink($ruta_anterior);
                    }
                }
            } else {
                $mensaje = "Error: No se pudo subir la imagen. Intenta nuevamente.";
                $nombre_imagen = $receta['imagen']; // Mantener imagen anterior
            }
        } else {
            $mensaje = "Error: Solo se permiten imágenes JPG, JPEG, PNG o GIF.";
            $nombre_imagen = $receta['imagen']; // Mantener imagen anterior
        }
    }
    
    // Actualizar los datos de la receta en la base de datos
    $stmt = $conexion->prepare("UPDATE recetas SET titulo = ?, descripcion = ?, ingredientes = ?, instrucciones = ?, imagen = ?, categoria_id = ? WHERE id = ?");
    $stmt->bind_param("sssssii", $titulo, $descripcion, $ingredientes, $instrucciones, $nombre_imagen, $categoria_id, $receta_id);
    
    // Ejecuta la actualización
    if ($stmt->execute()) {
        // Si se actualizó correctamente, muestra mensaje de éxito
        $mensaje = "¡Receta actualizada exitosamente!";
        // Actualiza los datos en la variable local para mostrar los cambios al usuario
        $receta['titulo'] = $titulo;
        $receta['descripcion'] = $descripcion;
        $receta['ingredientes'] = $ingredientes;
        $receta['instrucciones'] = $instrucciones;
        $receta['categoria_id'] = $categoria_id;
        $receta['imagen'] = $nombre_imagen;
    } else {
        // Si hubo error, muestra mensaje
        $mensaje = "Error al actualizar la receta. Intenta nuevamente.";
    }
    // Cierra la sentencia preparada
    $stmt->close();
}

// Obtener todas las categorías desde la base de datos para llenar el select
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Configuración básica del documento -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Editar Receta - RecepApp</title>
  <!-- Vincula el archivo CSS -->
  <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
  <!-- Fuente y librería de íconos -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a2d9d6cfd5.js" crossorigin="anonymous"></script>
</head>
<body>
  <!-- === NAVBAR === -->
  <header class="navbar">
    <div class="logo">
      <i class="fas fa-utensils"></i>
      <h1>RecepApp</h1>
    </div>
    <nav class="menu">
      <!-- Enlaces principales -->
      <a href="<?php echo url('index.php'); ?>">Inicio</a>
      <a href="<?php echo url('ver-mas-recetas.php'); ?>">Recetas</a>
      <?php if (usuarioEstaLogeado()): ?>
        <!-- Muestra el nombre del usuario -->
        <span class="user">Hola, <?php echo htmlspecialchars(obtenerUsuarioNombre()); ?></span>
        <div class="profile-menu">
          <!-- Botón de perfil -->
          <button class="profile-btn"><i class="fas fa-user"></i> Mi Perfil</button>
          <!-- Menú desplegable del perfil -->
          <div class="profile-dropdown">
            <a href="<?php echo url('ver-recetas-propias.php'); ?>"><i class="fas fa-book"></i> Mis Recetas</a>
            <a href="<?php echo url('ver-favoritos.php'); ?>"><i class="fas fa-heart"></i> Favoritos</a>
            <a href="<?php echo url('../backend/logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
          </div>
        </div>
      <?php else: ?>
        <!-- Si el usuario no está logueado, muestra opciones de acceso -->
        <a href="<?php echo url('iniciar-sesion.php'); ?>">Iniciar Sesión</a>
        <a href="<?php echo url('registrarse.php'); ?>">Registrarse</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- === HEADER DE PÁGINA === -->
  <section class="page-header">
    <h2><i class="fas fa-edit"></i> Editar Receta</h2>
    <p>Modifica los detalles de tu receta</p>
  </section>

  <!-- === FORMULARIO DE EDICIÓN === -->
  <section class="form-container">
    <div class="auth-form">
      <!-- Muestra el mensaje de confirmación o error -->
      <?php if ($mensaje): ?>
        <div class="<?php echo strpos($mensaje, 'Error') === false ? 'success-message' : 'error-message'; ?>">
          <?php echo $mensaje; ?>
        </div>
      <?php endif; ?>
      
      <!-- Formulario para editar la receta -->
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="titulo"><i class="fas fa-heading"></i> Título de la Receta</label>
          <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($receta['titulo']); ?>" required>
        </div>

        <div class="form-group">
          <label for="descripcion"><i class="fas fa-align-left"></i> Descripción</label>
          <textarea id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($receta['descripcion']); ?></textarea>
        </div>

        <div class="form-group">
          <label for="categoria_id"><i class="fas fa-layer-group"></i> Categoría</label>
          <select id="categoria_id" name="categoria_id" required>
            <option value="">Selecciona una categoría</option>
            <!-- Recorre las categorías desde la base de datos -->
            <?php while($categoria = $categorias->fetch_assoc()): ?>
              <option value="<?php echo $categoria['id']; ?>" <?php echo ($receta['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($categoria['nombre']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="imagen"><i class="fas fa-image"></i> Imagen de la Receta</label>
          <!-- Si la receta tiene una imagen, la muestra -->
          <?php if (!empty($receta['imagen'])): ?>
            <div style="margin-bottom: 10px;">
              <img src="<?php echo asset('img/' . $receta['imagen']); ?>" alt="Imagen actual" style="max-width: 200px; border-radius: 8px;" id="current-image">
              <p><small>Imagen actual</small></p>
            </div>
          <?php endif; ?>
          <!-- Campo para subir nueva imagen -->
          <input type="file" id="imagen" name="imagen" accept="image/*">
          <small>Deja vacío para mantener la imagen actual. Formatos: JPG, PNG, GIF</small>
          <!-- Vista previa de imagen -->
          <div id="imagen-preview" style="margin-top: 10px; display: none;">
            <img id="preview-img" src="/placeholder.svg" alt="Vista previa" style="max-width: 300px; max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p><small>Vista previa de la nueva imagen</small></p>
          </div>
        </div>

        <div class="form-group">
          <label for="ingredientes"><i class="fas fa-shopping-basket"></i> Ingredientes</label>
          <textarea id="ingredientes" name="ingredientes" rows="6" required><?php echo htmlspecialchars($receta['ingredientes']); ?></textarea>
          <small>Un ingrediente por línea</small>
        </div>

        <div class="form-group">
          <label for="instrucciones"><i class="fas fa-list-ol"></i> Instrucciones</label>
          <textarea id="instrucciones" name="instrucciones" rows="8" required><?php echo htmlspecialchars($receta['instrucciones']); ?></textarea>
          <small>Un paso por línea</small>
        </div>

        <!-- Botón principal para guardar cambios -->
        <button type="submit" class="btn-primary">
          <i class="fas fa-save"></i> Guardar Cambios
        </button>
        
        <!-- Enlace para volver atrás -->
        <a href="<?php echo url('ver-recetas-propias.php'); ?>" class="btn-secondary">
          <i class="fas fa-arrow-left"></i> Volver a Mis Recetas
        </a>
        
        <!-- Enlace para ver la receta -->
        <a href="<?php echo url('ver-receta.php?id=' . $receta_id); ?>" class="btn-secondary">
          <i class="fas fa-eye"></i> Ver Receta
        </a>
      </form>
    </div>
  </section>

  <!-- === FOOTER === -->
  <footer class="footer-simple">
    <p>© 2025 RecepApp — Contacto: <a href="mailto:contacto@recepapp.com">contacto@recepapp.com</a></p>
  </footer>

  <!-- === SCRIPT PARA MENÚ DE PERFIL Y VISTA PREVIA === -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const profileBtn = document.querySelector('.profile-btn');
      if (profileBtn) {
        // Muestra u oculta el menú al hacer clic en el botón
        profileBtn.addEventListener('click', function() {
          document.querySelector('.profile-dropdown').classList.toggle('show');
        });
      }
      
      // Cierra el menú si se hace clic fuera de él
      window.addEventListener('click', function(e) {
        if (!e.target.matches('.profile-btn')) {
          const dropdown = document.querySelector('.profile-dropdown');
          if (dropdown && dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
          }
        }
      });

      // Vista previa de imagen (igual que en agregar receta)
      document.getElementById('imagen').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(event) {
            const preview = document.getElementById('imagen-preview');
            const img = document.getElementById('preview-img');
            img.src = event.target.result;
            preview.style.display = 'block';
            
            // Oculta la imagen actual cuando se selecciona una nueva
            const currentImage = document.getElementById('current-image');
            if (currentImage) {
              currentImage.style.display = 'none';
            }
          };
          reader.readAsDataURL(file);
        } else {
          // Si no hay archivo seleccionado, muestra la imagen actual nuevamente
          const preview = document.getElementById('imagen-preview');
          preview.style.display = 'none';
          
          const currentImage = document.getElementById('current-image');
          if (currentImage) {
            currentImage.style.display = 'block';
          }
        }
      });
    });
  </script>
</body>
</html>