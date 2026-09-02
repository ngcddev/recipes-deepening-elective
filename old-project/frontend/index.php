<?php
// Incluye el archivo que gestiona la sesión de usuario (login, logout, etc.)
require_once __DIR__ . '/../backend/session.php';

// Incluye el archivo que establece la conexión a la base de datos
require_once __DIR__ . '/../backend/conexion.php';

// Incluye funciones auxiliares como 'url()' y 'asset()' para manejar rutas y recursos
require_once '../backend/helpers.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Configuración básica del documento -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cocina Fácil</title>

  <!-- Enlace al archivo de estilos CSS -->
  <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">

  <!-- Fuente de Google para mejorar la apariencia del texto -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

  <!-- Íconos de Font Awesome (para utensilios, usuario, corazón, etc.) -->
  <script src="https://kit.fontawesome.com/a2d9d6cfd5.js" crossorigin="anonymous"></script>
</head>
<body>
<!-- === NAVBAR === -->
<header class="navbar">
  <div class="logo">
    <!-- Ícono de utensilios y nombre de la aplicación -->
    <i class="fas fa-utensils"></i>
    <h1>RecepApp</h1>
  </div>

  <nav class="menu">
    <!-- Enlaces principales de navegación -->
    <a href="<?php echo url('index.php'); ?>">Inicio</a>
    <a href="<?php echo url('ver-mas-recetas.php'); ?>">Recetas</a>

    <!-- Verifica si el usuario ha iniciado sesión -->
    <?php if (usuarioEstaLogeado()): ?>
      <!-- Si está logueado, muestra su nombre -->
      <span class="user">Hola, <?php echo htmlspecialchars(obtenerUsuarioNombre()); ?></span>
      
      <!-- Menú de perfil desplegable -->
      <div class="profile-menu">
        <button class="profile-btn"><i class="fas fa-user"></i> Mi Perfil</button>

        <!-- Contenido del menú desplegable -->
        <div class="profile-dropdown">
          <a href="<?php echo url('ver-recetas-propias.php'); ?>"><i class="fas fa-book"></i> Mis Recetas</a>
          <a href="<?php echo url('ver-favoritos.php'); ?>"><i class="fas fa-heart"></i> Favoritos</a>
          <a href="<?php echo url('../backend/logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
      </div>
    <?php else: ?>
      <!-- Si no está logueado, muestra opciones de inicio de sesión o registro -->
      <a href="<?php echo url('iniciar-sesion.php'); ?>">Iniciar Sesión</a>
      <a href="<?php echo url('registrarse.php'); ?>">Registrarse</a>
    <?php endif; ?>
  </nav>
</header>

<!-- === SECCIÓN PRINCIPAL (HERO) === -->
<section class="hero">
  <h2>Descubre tu próxima receta favorita 🍰</h2>
  <p>Busca entre cientos de recetas fáciles y deliciosas. Filtra por categoría o guarda tus favoritas.</p>

  <!-- Barra de búsqueda de recetas -->
  <form action="<?php echo url('ver-mas-recetas.php'); ?>" method="GET" class="search-container">
    <input type="text" name="busqueda" placeholder="Buscar recetas..." />
    <button type="submit"><i class="fas fa-search"></i> Buscar</button>
  </form>
</section>

<!-- === CATEGORÍAS POPULARES === -->
<section class="categorias">
  <h3>Categorías Populares</h3>
  <div class="cat-grid">
    <?php
    // Consulta para obtener las primeras 4 categorías de la base de datos
    $categorias = $conexion->query("SELECT * FROM categorias LIMIT 4");

    // Bucle para mostrar cada categoría
    while($categoria = $categorias->fetch_assoc()):
      // Asignar imágenes específicas según el nombre de la categoría
      $imagenes_categorias = [
        'Postres' => 'pastel de chocolate.jpg',
        'Ensaladas' => 'ensalada fresca.jpg',
        'Sopas' => 'sopa de verduras.jpg',
        'Platos Principales' => 'pollo al horno.jpg',
        'Desayunos' => 'brownies-237776_1280.jpg',
        'Bebidas' => 'ensalada de frutas.jpg'
      ];

      // Si la categoría no tiene imagen definida, usa un placeholder
      $imagen_categoria = $imagenes_categorias[$categoria['nombre']] ?? 'placeholder.jpg';
    ?>
    <!-- Cada categoría se muestra como una tarjeta con imagen y nombre -->
    <a href="<?php echo url('ver-mas-recetas.php?categoria=' . $categoria['id']); ?>" class="cat-card-link">
      <div class="cat-card">
        <img src="<?php echo asset('img/' . $imagen_categoria); ?>" 
             alt="<?php echo $categoria['nombre']; ?>" 
             onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">
        <h4><?php echo $categoria['nombre']; ?></h4>
      </div>
    </a>
    <?php endwhile; ?>
  </div>
</section>

<!-- === RECETAS DESTACADAS === -->
<section class="recetas-destacadas">
  <h3>Recetas Destacadas</h3>
  <div class="recetas-grid">
    <?php
    // Consulta para obtener las últimas 6 recetas agregadas con su categoría
    $recetas = $conexion->query("SELECT r.*, c.nombre as categoria_nombre 
                                FROM recetas r 
                                LEFT JOIN categorias c ON r.categoria_id = c.id 
                                ORDER BY r.fecha_creacion DESC 
                                LIMIT 6");

    // Si hay recetas disponibles
    if ($recetas->num_rows > 0):
      while($receta = $recetas->fetch_assoc()):
        // Mensajes de depuración (solo visibles en el código fuente)
        echo "<!-- DEBUG Receta ID: " . $receta['id'] . " -->";
        echo "<!-- DEBUG Título: " . $receta['titulo'] . " -->";
        echo "<!-- DEBUG Imagen en BD: " . $receta['imagen'] . " -->";

        // Ruta física para comprobar si la imagen existe
        $ruta_completa = __DIR__ . '/../img/' . $receta['imagen'];
        echo "<!-- DEBUG Ruta completa: " . $ruta_completa . " -->";
        echo "<!-- DEBUG Existe archivo: " . (file_exists($ruta_completa) ? 'SÍ' : 'NO') . " -->";

        // Ruta pública de la imagen
        $imagen_ruta = 'img/' . $receta['imagen'];
    ?>
    <!-- Tarjeta individual de receta -->
<!-- Tarjeta individual de receta -->
<div class="card">
  <img src="<?php echo asset($imagen_ruta); ?>" 
       alt="<?php echo htmlspecialchars($receta['titulo']); ?>"
       onerror="console.log('Error cargando imagen: <?php echo $receta['imagen']; ?>'); this.src='<?php echo asset('img/placeholder.jpg'); ?>'">
  
  <div class="card-content">
    <span class="categoria-badge"><?php echo htmlspecialchars($receta['categoria_nombre']); ?></span>
    <h4><?php echo htmlspecialchars($receta['titulo']); ?></h4>
    <p class="descripcion"><?php echo substr(htmlspecialchars($receta['descripcion']), 0, 100); ?>...</p>
    
  <div class="card-meta">
    <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($receta['fecha_creacion'])); ?></span>
  </div>
    
    <div class="card-actions">
      <a href="<?php echo url('ver-receta.php?id=' . $receta['id']); ?>" class="btn-ver">
        <i class="fas fa-eye"></i> Ver Receta
      </a>
    </div>
  </div>
</div>
    <?php 
      endwhile;
    else:
      // Si no hay recetas, muestra mensaje
      echo "<p>No hay recetas disponibles aún.</p>";
    endif;
    ?>
  </div>
</section>

<!-- === PIE DE PÁGINA === -->
<footer class="footer-simple">
  <p>© 2025 RecepApp — Contacto: <a href="mailto:contacto@recepapp.com">contacto@recepapp.com</a></p>
</footer>

<!-- === SCRIPT PARA MENÚ DE PERFIL === -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const profileBtn = document.querySelector('.profile-btn');

    // Si el botón del perfil existe, agrega evento de clic para mostrar el menú desplegable
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
