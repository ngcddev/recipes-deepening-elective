<?php
// Inicia la sesión del usuario y carga los archivos necesarios
require_once __DIR__ . '/../backend/session.php';   // Control de sesión (para saber si el usuario está logeado)
require_once __DIR__ . '/../backend/conexion.php';  // Conexión con la base de datos
require_once '../backend/helpers.php';              // Funciones auxiliares (como url(), asset(), etc.)

// --- Verificación de acceso ---
// Si el usuario no ha iniciado sesión, se le redirige al login
if (!usuarioEstaLogeado()) {
    header('Location: iniciar-sesion.php');
    exit;
}

// Se guarda el ID del usuario logeado desde la sesión
$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';

// --- Procesar la eliminación de una receta ---
if (isset($_POST['eliminar_receta'])) {
    $receta_id = intval($_POST['eliminar_receta']);  // Se obtiene el ID de la receta a eliminar y se convierte a número entero
    
    // Verifica que la receta realmente pertenece al usuario actual
    $stmt = $conexion->prepare("SELECT id FROM recetas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $receta_id, $usuario_id);
    $stmt->execute();
    
    // Si la receta pertenece al usuario, se procede a eliminarla
    if ($stmt->get_result()->num_rows > 0) {
        
        // ✅ CORRECCIÓN: Eliminar primero los registros relacionados
        try {
            // 1. Eliminar comentarios relacionados
            $stmt_comentarios = $conexion->prepare("DELETE FROM comentarios WHERE receta_id = ?");
            $stmt_comentarios->bind_param("i", $receta_id);
            $stmt_comentarios->execute();
            $stmt_comentarios->close();
            
            // 2. Eliminar favoritos relacionados
            $stmt_favoritos = $conexion->prepare("DELETE FROM favoritos WHERE receta_id = ?");
            $stmt_favoritos->bind_param("i", $receta_id);
            $stmt_favoritos->execute();
            $stmt_favoritos->close();
            
            // 3. Ahora eliminar la receta
            $stmt_receta = $conexion->prepare("DELETE FROM recetas WHERE id = ?");
            $stmt_receta->bind_param("i", $receta_id);
            
            if ($stmt_receta->execute()) {
                $mensaje = "✅ Receta eliminada exitosamente";
            } else {
                $mensaje = "❌ Error al eliminar la receta";
            }
            $stmt_receta->close();
            
        } catch (Exception $e) {
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    } else {
        $mensaje = "❌ No tienes permisos para eliminar esta receta";
    }
    $stmt->close();
}

// --- Obtener todas las recetas del usuario actual ---
$recetas = $conexion->query("
    SELECT r.*, c.nombre as categoria_nombre 
    FROM recetas r 
    LEFT JOIN categorias c ON r.categoria_id = c.id 
    WHERE r.usuario_id = $usuario_id 
    ORDER BY r.fecha_creacion DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mis Recetas - RecepApp</title>

  <!-- Estilos y fuentes -->
  <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
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
    
    <!-- Menú de navegación -->
    <nav class="menu">
      <a href="<?php echo url('index.php'); ?>">Inicio</a>
      <a href="<?php echo url('ver-mas-recetas.php'); ?>">Recetas</a>
      
      <!-- Si el usuario está logeado, se muestra su nombre y el menú de perfil -->
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
      
      <!-- Si no está logeado, se muestran los botones de acceso -->
      <?php else: ?>
        <a href="<?php echo url('iniciar-sesion.php'); ?>">Iniciar Sesión</a>
        <a href="<?php echo url('registrarse.php'); ?>">Registrarse</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- === ENCABEZADO DE PÁGINA === -->
  <section class="page-header">
    <h2><i class="fas fa-book"></i> Mis Recetas</h2>
    <p>Gestiona todas las recetas que has creado</p>
    <!-- Botón para agregar una nueva receta -->
    <a href="<?php echo url('agregar-receta.php'); ?>" class="btn-primary">
      <i class="fas fa-plus"></i> Agregar Nueva Receta
    </a>
  </section>

  <!-- === LISTADO DE RECETAS DEL USUARIO === -->
  <section class="recetas-lista">
    <?php if ($recetas->num_rows > 0): ?>
      <div class="recetas-grid">
        <!-- Bucle para mostrar todas las recetas del usuario -->
        <?php while($receta = $recetas->fetch_assoc()): ?>
          <div class="card">
            <!-- Imagen de la receta -->
            <img src="<?php echo asset('img/' . ($receta['imagen'] ?: 'placeholder.jpg')); ?>" 
                 alt="<?php echo htmlspecialchars($receta['titulo']); ?>"
                 onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">
            
            <div class="card-content">
              <!-- Categoría -->
              <span class="categoria-badge"><?php echo htmlspecialchars($receta['categoria_nombre']); ?></span>
              
              <!-- Título de la receta -->
              <h4><?php echo htmlspecialchars($receta['titulo']); ?></h4>
              
              <!-- Descripción breve -->
              <p class="descripcion"><?php echo substr(htmlspecialchars($receta['descripcion']), 0, 100); ?>...</p>
              
              <!-- Metadatos -->
              <div class="card-meta">
                <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($receta['fecha_creacion'])); ?></span>
                <span><i class="fas fa-eye"></i> <?php echo rand(10, 100); ?> vistas</span>
              </div>
              
              <!-- Botones de acción -->
              <div class="card-actions">
                <!-- Ver receta -->
                <a href="<?php echo url('ver-receta.php?id=' . $receta['id']); ?>" class="btn-ver">
                  <i class="fas fa-eye"></i> Ver
                </a>
                
                <!-- Editar receta -->
                <a href="<?php echo url('editar-receta.php?id=' . $receta['id']); ?>" class="btn-editar">
                  <i class="fas fa-edit"></i> Editar
                </a>
                
                <!-- Eliminar receta -->
                <form method="POST" class="eliminar-receta-form">
                  <button type="submit" 
                          name="eliminar_receta" 
                          value="<?php echo $receta['id']; ?>" 
                          class="btn-eliminar"
                          onclick="return confirm('¿Estás seguro de eliminar esta receta? Esta acción no se puede deshacer.')">
                    <i class="fas fa-trash"></i> Eliminar
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <!-- Si el usuario no tiene recetas -->
      <div class="no-resultados">
        <i class="fas fa-book" style="font-size: 4rem; color: #f1cadd; margin-bottom: 20px;"></i>
        <h3>No has creado ninguna receta aún</h3>
        <p>Comparte tus recetas favoritas con la comunidad de RecepApp.</p>
      </div>
    <?php endif; ?>
  </section>

  <!-- === FOOTER === -->
  <footer class="footer-simple">
    <p>© 2025 RecepApp — Contacto: <a href="mailto:contacto@recepapp.com">contacto@recepapp.com</a></p>
  </footer>

  <!-- === SCRIPT DEL MENÚ DE PERFIL === -->
  <script>
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
