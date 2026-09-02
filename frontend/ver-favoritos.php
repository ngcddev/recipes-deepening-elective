<?php
// ===========================================================
//     BLOQUE PHP: MOSTRAR Y GESTIONAR RECETAS FAVORITAS
// ===========================================================

// Se incluyen los archivos necesarios del sistema
require_once __DIR__ . '/../backend/session.php';   // Manejo de sesión de usuario
require_once __DIR__ . '/../backend/conexion.php';  // Conexión a la base de datos
require_once '../backend/helpers.php';              // Funciones auxiliares (url, asset, etc.)

// -----------------------------------------------------------
// Verificar si el usuario está logeado antes de continuar
// -----------------------------------------------------------
if (!usuarioEstaLogeado()) { // Si no hay sesión iniciada
    header('Location: iniciar-sesion.php'); // Redirige al login
    exit; // Detiene la ejecución del código
}

// Se obtiene el ID del usuario desde la sesión actual
$usuario_id = $_SESSION['usuario_id'];

// -----------------------------------------------------------
// Procesar eliminación de una receta de la lista de favoritos
// -----------------------------------------------------------
if (isset($_POST['eliminar_favorito'])) { // Si se envió el formulario de "Quitar favorito"
    $receta_id = intval($_POST['eliminar_favorito']); // Convierte a número entero el ID recibido
    
    // Se prepara la consulta SQL para eliminar de la tabla favoritos
    $stmt = $conexion->prepare("DELETE FROM favoritos WHERE usuario_id = ? AND receta_id = ?");
    $stmt->bind_param("ii", $usuario_id, $receta_id); // Enlaza los parámetros
    $stmt->execute(); // Ejecuta la eliminación
    $stmt->close();   // Cierra la consulta preparada
    
    // Redirige a la misma página para evitar reenviar el formulario al recargar
    header("Location: ver-favoritos.php");
    exit;
}

// -----------------------------------------------------------
// Consultar todas las recetas favoritas del usuario logeado
// -----------------------------------------------------------
$favoritos = $conexion->query("
    SELECT r.*, c.nombre as categoria_nombre, u.nombre as usuario_nombre 
    FROM recetas r 
    LEFT JOIN categorias c ON r.categoria_id = c.id 
    LEFT JOIN usuarios u ON r.usuario_id = u.id 
    INNER JOIN favoritos f ON r.id = f.receta_id 
    WHERE f.usuario_id = $usuario_id 
    ORDER BY f.fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Configuración básica -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Título de la página -->
  <title>Mis Favoritos - RecepApp</title>

  <!-- Enlace al archivo CSS principal -->
  <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">

  <!-- Fuente de Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

  <!-- Librería de íconos FontAwesome -->
  <script src="https://kit.fontawesome.com/a2d9d6cfd5.js" crossorigin="anonymous"></script>
</head>

<body>
<!-- ======================================================= -->
<!-- NAVBAR (barra superior con logo, menú y perfil usuario) -->
<!-- ======================================================= -->
<header class="navbar">
  <div class="logo">
    <!-- Ícono y nombre del sitio -->
    <i class="fas fa-utensils"></i>
    <h1>RecepApp</h1>
  </div>

  <nav class="menu">
    <!-- Enlaces principales del sitio -->
    <a href="<?php echo url('index.php'); ?>">Inicio</a>
    <a href="<?php echo url('ver-mas-recetas.php'); ?>">Recetas</a>

    <!-- Si el usuario ha iniciado sesión, mostrar su menú de perfil -->
    <?php if (usuarioEstaLogeado()): ?>
      <span class="user">Hola, <?php echo htmlspecialchars(obtenerUsuarioNombre()); ?></span>
      
      <div class="profile-menu">
        <button class="profile-btn"><i class="fas fa-user"></i> Mi Perfil</button>

        <!-- Menú desplegable con opciones del usuario -->
        <div class="profile-dropdown">
          <a href="<?php echo url('ver-recetas-propias.php'); ?>"><i class="fas fa-book"></i> Mis Recetas</a>
          <a href="<?php echo url('ver-favoritos.php'); ?>"><i class="fas fa-heart"></i> Favoritos</a>
          <a href="<?php echo url('../backend/logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
      </div>

    <!-- Si no hay sesión, mostrar opciones de acceso -->
    <?php else: ?>
      <a href="<?php echo url('iniciar-sesion.php'); ?>">Iniciar Sesión</a>
      <a href="<?php echo url('registrarse.php'); ?>">Registrarse</a>
    <?php endif; ?>
  </nav>
</header>

<!-- ======================================================= -->
<!-- ENCABEZADO DE LA PÁGINA -->
<!-- ======================================================= -->
<section class="page-header">
  <h2><i class="fas fa-heart"></i> Mis Recetas Favoritas</h2>
  <p>Aquí encontrarás todas las recetas que has guardado como favoritas</p>
</section>

<!-- ======================================================= -->
<!-- LISTA DE RECETAS FAVORITAS -->
<!-- ======================================================= -->
<section class="recetas-lista">
  <?php if ($favoritos->num_rows > 0): ?> <!-- Si existen recetas favoritas -->
    <div class="recetas-grid">
      <?php while($receta = $favoritos->fetch_assoc()): ?> <!-- Bucle para mostrar cada receta -->
        <div class="card">
          
          <!-- Imagen de la receta -->
          <img src="<?php echo asset('img/' . ($receta['imagen'] ?: 'placeholder.jpg')); ?>" 
               alt="<?php echo htmlspecialchars($receta['titulo']); ?>"
               onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">

          <!-- Contenido dentro de la tarjeta -->
          <div class="card-content">
            <!-- Nombre de la categoría -->
            <span class="categoria-badge"><?php echo htmlspecialchars($receta['categoria_nombre']); ?></span>
            
            <!-- Título de la receta -->
            <h4><?php echo htmlspecialchars($receta['titulo']); ?></h4>
            
            <!-- Descripción abreviada -->
            <p class="descripcion"><?php echo substr(htmlspecialchars($receta['descripcion']), 0, 100); ?>...</p>
            
            <!-- Información del autor y la fecha -->
            <div class="card-meta">
              <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($receta['usuario_nombre']); ?></span>
              <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($receta['fecha_creacion'])); ?></span>
            </div>

            <!-- Botones de acción -->
            <div class="card-actions">
              <!-- Ver receta completa -->
              <a href="<?php echo url('ver-receta.php?id=' . $receta['id']); ?>" class="btn-ver">
                <i class="fas fa-eye"></i> Ver Receta
              </a>

              <!-- Botón para eliminar de favoritos -->
              <form method="POST" class="eliminar-favorito-form">
                <button type="submit" 
                        name="eliminar_favorito" 
                        value="<?php echo $receta['id']; ?>" 
                        class="btn-eliminar"
                        onclick="return confirm('¿Quitar de favoritos?')">
                  <i class="fas fa-heart-broken"></i> Quitar
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

  <?php else: ?>
    <!-- Si el usuario no tiene favoritos, muestra este mensaje -->
    <div class="no-resultados">
      <i class="fas fa-heart" style="font-size: 4rem; color: #f1cadd; margin-bottom: 20px;"></i>
      <h3>No tienes recetas favoritas aún</h3>
      <p>Descubre recetas deliciosas y guárdalas como favoritas para encontrarlas fácilmente después.</p>
      <a href="<?php echo url('ver-mas-recetas.php'); ?>" class="btn-primary">
        <i class="fas fa-search"></i> Explorar Recetas
      </a>
    </div>
  <?php endif; ?>
</section>

<!-- ======================================================= -->
<!-- PIE DE PÁGINA -->
<!-- ======================================================= -->
<footer class="footer-simple">
  <p>© 2025 RecepApp — Contacto: 
     <a href="mailto:contacto@recepapp.com">contacto@recepapp.com</a></p>
</footer>

<!-- ======================================================= -->
<!-- SCRIPT JS (Mostrar/Ocultar menú del perfil) -->
<!-- ======================================================= -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const profileBtn = document.querySelector('.profile-btn');
    
    if (profileBtn) {
      profileBtn.addEventListener('click', function() {
        document.querySelector('.profile-dropdown').classList.toggle('show');
      });
    }
    
    // Cierra el menú si el usuario hace clic fuera de él
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
