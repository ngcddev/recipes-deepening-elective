<?php
// Incluye el archivo de sesión para manejar usuarios logueados
require_once __DIR__ . '/../backend/session.php';
// Incluye la conexión a la base de datos
require_once __DIR__ . '/../backend/conexion.php';
// Incluye funciones auxiliares definidas en helpers.php
require_once '../backend/helpers.php';

// Verifica si el usuario está logueado
if (!usuarioEstaLogeado()) {
    // Si no está logueado, redirige a la página de inicio de sesión
    header('Location: iniciar-sesion.php');
    exit; // Detiene la ejecución del script
}

// Obtiene el ID del usuario desde la sesión activa
$usuario_id = $_SESSION['usuario_id'];
// Inicializa un mensaje vacío para mostrar errores o confirmaciones
$mensaje = '';

// Verifica si el formulario fue enviado mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los datos enviados desde el formulario
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $ingredientes = trim($_POST['ingredientes']);
    $instrucciones = trim($_POST['instrucciones']);
    $categoria_id = intval($_POST['categoria_id']);
    
    // Verifica si se subió una imagen correctamente
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== 0) {
        // Si no hay imagen o hay error, muestra mensaje
        $mensaje = "Error: Debes subir una imagen para la receta.";
    } else {
        // Obtiene la extensión del archivo subido
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        // Define extensiones permitidas
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Verifica si la extensión es válida
        if (!in_array(strtolower($extension), $extensiones_permitidas)) {
            $mensaje = "Error: Solo se permiten imágenes JPG, JPEG, PNG o GIF.";
        } else {
            // Genera un nombre único para la imagen
            $nombre_imagen = uniqid('receta_') . '.' . $extension;
            // Define la ruta donde se guardará la imagen
            $ruta_destino = __DIR__ . '/img/' . $nombre_imagen;
            
            // Verifica si existe el directorio de imágenes
            $img_dir = __DIR__ . '/img';
            if (!is_dir($img_dir)) {
                // Si no existe, lo crea con permisos 755
                mkdir($img_dir, 0755, true);
            }
            
            // Mueve la imagen subida a la carpeta destino
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                // Si ocurre un error al moverla, muestra mensaje
                $mensaje = "Error: No se pudo subir la imagen. Intenta nuevamente.";
            } else {
                // Si la imagen se subió correctamente, inserta la receta en la base de datos
                $stmt = $conexion->prepare("INSERT INTO recetas (titulo, descripcion, ingredientes, instrucciones, imagen, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                // Asocia los valores a los parámetros de la consulta
                $stmt->bind_param("sssssii", $titulo, $descripcion, $ingredientes, $instrucciones, $nombre_imagen, $categoria_id, $usuario_id);
                
                // Ejecuta la consulta
                if ($stmt->execute()) {
                    // Si se inserta correctamente, muestra mensaje de éxito
                    $mensaje = "¡Receta agregada exitosamente!";
                    // Limpia los campos del formulario
                    $titulo = $descripcion = $ingredientes = $instrucciones = '';
                    $categoria_id = 0;
                } else {
                    // Si falla la inserción, muestra mensaje de error
                    $mensaje = "Error al agregar la receta. Intenta nuevamente.";
                }
                // Cierra la sentencia preparada
                $stmt->close();
            }
        }
    }
}

// Consulta las categorías disponibles para llenar el select del formulario
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agregar Receta - RecepApp</title>
  <!-- Enlace al archivo CSS -->
  <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
  <!-- Encabezado de la página -->
  <header class="navbar">
    <div class="logo">
      <i class="fas fa-utensils"></i>
      <h1>RecepApp</h1>
    </div>
    <nav class="menu">
      <!-- Enlaces de navegación -->
      <a href="<?php echo url('index.php'); ?>">Inicio</a>
      <a href="<?php echo url('ver-mas-recetas.php'); ?>">Recetas</a>
      <?php if (usuarioEstaLogeado()): ?>
        <span class="user">Hola, <?php echo htmlspecialchars(obtenerUsuarioNombre()); ?></span>
        <div class="profile-menu">
          <button class="profile-btn"><i class="fas fa-user"></i> Mi Perfil</button>

          <!-- Menú desplegable del perfil -->
          <div class="profile-dropdown">
            <a href="<?php echo url('ver-recetas-propias.php'); ?>" class="active"><i class="fas fa-book"></i> Mis Recetas</a>
            <a href="<?php echo url('ver-favoritos.php'); ?>"><i class="fas fa-heart"></i> Favoritos</a>
            <a href="<?php echo url('../backend/logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
          </div>
        </div>
      <?php else: ?>
        <!-- Si no está logueado, muestra los enlaces de acceso -->
        <a href="<?php echo url('iniciar-sesion.php'); ?>">Iniciar Sesión</a>
        <a href="<?php echo url('registrarse.php'); ?>">Registrarse</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- Encabezado de sección -->
  <section class="page-header">
    <h2>Agregar Nueva Receta</h2>
  </section>

  <!-- Contenedor del formulario -->
  <section class="form-container">
    <div class="auth-form">
      <!-- Muestra mensaje de éxito o error -->
      <?php if ($mensaje): ?>
        <div class="<?php echo strpos($mensaje, 'Error') === false ? 'success-message' : 'error-message'; ?>">
          <?php echo $mensaje; ?>
        </div>
      <?php endif; ?>
      
      <!-- Formulario para agregar receta -->
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label>Título</label>
          <input type="text" name="titulo" value="<?php echo isset($titulo) ? htmlspecialchars($titulo) : ''; ?>" required>
        </div>

        <div class="form-group">
          <label>Descripción</label>
          <textarea name="descripcion" rows="3" required><?php echo isset($descripcion) ? htmlspecialchars($descripcion) : ''; ?></textarea>
        </div>

        <div class="form-group">
          <label>Categoría</label>
          <select name="categoria_id" required>
            <option value="">Selecciona categoría</option>
            <!-- Ciclo que muestra las categorías desde la base de datos -->
            <?php while($categoria = $categorias->fetch_assoc()): ?>
              <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <!-- Campo para subir imagen -->
          <label>Imagen <span style="color: red;">*</span></label>
          <input type="file" name="imagen" accept="image/*" id="imagen-input" required>
          <small style="color: #666; display: block; margin-top: 5px;">La imagen es obligatoria. Formatos permitidos: JPG, JPEG, PNG, GIF</small>
          <!-- Vista previa de imagen -->
          <div id="imagen-preview" style="margin-top: 10px; display: none;">
            <img id="preview-img" src="/placeholder.svg" alt="Vista previa" style="max-width: 300px; max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          </div>
        </div>

        <div class="form-group">
          <label>Ingredientes</label>
          <textarea name="ingredientes" rows="6" required><?php echo isset($ingredientes) ? htmlspecialchars($ingredientes) : ''; ?></textarea>
        </div>

        <div class="form-group">
          <label>Instrucciones</label>
          <textarea name="instrucciones" rows="8" required><?php echo isset($instrucciones) ? htmlspecialchars($instrucciones) : ''; ?></textarea>
        </div>

        <!-- Botón para enviar el formulario -->
        <button type="submit" class="btn-primary">Publicar Receta</button>
      </form>
    </div>
  </section>

  <!-- Script JavaScript para vista previa de imagen -->
  <script>
    // Escucha el cambio en el input de imagen
    document.getElementById('imagen-input').addEventListener('change', function(e) {
      const file = e.target.files[0]; // Obtiene el archivo seleccionado
      if (file) {
        const reader = new FileReader(); // Crea un lector de archivos
        // Cuando la imagen se carga, muestra la vista previa
        reader.onload = function(event) {
          const preview = document.getElementById('imagen-preview');
          const img = document.getElementById('preview-img');
          img.src = event.target.result; // Asigna la imagen leída
          preview.style.display = 'block'; // Muestra el contenedor
        };
        // Lee el archivo como URL base64
        reader.readAsDataURL(file);
      }
    });
  // SCRIPT DEL MENÚ DE PERFIL
    document.addEventListener('DOMContentLoaded', function() {
      const profileBtn = document.querySelector('.profile-btn');
      if (profileBtn) {
        // Muestra u oculta el menú desplegable del perfil al hacer clic
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
    });
  </script>
</body>
</html>
