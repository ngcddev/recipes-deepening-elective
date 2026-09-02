<?php
// Se incluyen archivos necesarios para manejo de sesión, conexión a base de datos y funciones auxiliares
require_once __DIR__ . '/../backend/session.php';
require_once __DIR__ . '/../backend/conexion.php';
require_once '../backend/helpers.php';

// Obtener parámetros de búsqueda y filtro desde la URL (GET)
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : ''; // Palabra clave de búsqueda
$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0; // ID de categoría seleccionada

// Consulta base para obtener recetas junto con su categoría y usuario
$sql = "SELECT r.*, c.nombre as categoria_nombre, u.nombre as usuario_nombre 
        FROM recetas r 
        LEFT JOIN categorias c ON r.categoria_id = c.id 
        LEFT JOIN usuarios u ON r.usuario_id = u.id 
        WHERE 1=1";

$params = []; // Arreglo para parámetros dinámicos
$types = '';  // Tipos de datos para bind_param

// Si hay búsqueda, se agregan condiciones con LIKE
if (!empty($busqueda)) {
    $sql .= " AND (r.titulo LIKE ? OR r.descripcion LIKE ? OR r.ingredientes LIKE ?)";
    $search_term = "%$busqueda%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sss';
}

// Si se selecciona una categoría específica
if ($categoria_id > 0) {
    $sql .= " AND r.categoria_id = ?";
    $params[] = $categoria_id;
    $types .= 'i';
}

// Ordena los resultados por fecha descendente
$sql .= " ORDER BY r.fecha_creacion DESC";

// Se prepara la consulta
$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params); // Se enlazan parámetros si existen
}
$stmt->execute();
$recetas = $stmt->get_result(); // Se obtienen los resultados

// Se obtienen las categorías para el menú de filtro
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Todas las Recetas - RecepApp</title>
  <!-- Estilos y fuentes -->
  <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a2d9d6cfd5.js" crossorigin="anonymous"></script>
</head>
<body>
  <!-- === BARRA DE NAVEGACIÓN === -->
  <header class="navbar">
    <div class="logo">
      <i class="fas fa-utensils"></i>
      <h1>RecepApp</h1>
    </div>
    <nav class="menu">
      <a href="<?php echo url('index.php'); ?>">Inicio</a>
      <a href="<?php echo url('ver-mas-recetas.php'); ?>" class="active">Recetas</a>
      <?php if (usuarioEstaLogeado()): ?> <!-- Si el usuario ha iniciado sesión -->
        <span class="user">Hola, <?php echo htmlspecialchars(obtenerUsuarioNombre()); ?></span>
        <div class="profile-menu">
          <button class="profile-btn"><i class="fas fa-user"></i> Mi Perfil</button>
          <div class="profile-dropdown">
            <a href="<?php echo url('ver-recetas-propias.php'); ?>"><i class="fas fa-book"></i> Mis Recetas</a>
            <a href="<?php echo url('ver-favoritos.php'); ?>"><i class="fas fa-heart"></i> Favoritos</a>
            <a href="<?php echo url('../backend/logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
          </div>
        </div>
      <?php else: ?> <!-- Si no ha iniciado sesión -->
        <a href="<?php echo url('iniciar-sesion.php'); ?>">Iniciar Sesión</a>
        <a href="<?php echo url('registrarse.php'); ?>">Registrarse</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- === ENCABEZADO DE PÁGINA === -->
  <section class="page-header">
    <h2><i class="fas fa-book-open"></i> Todas las Recetas</h2>
    <p>Descubre todas las recetas de nuestra comunidad</p>
  </section>

<!-- === FILTROS Y BÚSQUEDA === -->
<section class="filtros">
    <div class="filtros-container">
        <form method="GET" class="filtros-form">
            <!-- Campo de búsqueda -->
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" 
                       name="busqueda" 
                       value="<?php echo htmlspecialchars($busqueda); ?>" 
                       placeholder="Buscar recetas...">
            </div>
            
            <!-- Selector de categoría -->
            <select name="categoria">
                <option value="0">Todas las categorías</option>
                <?php 
                $categorias_select = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
                while($categoria = $categorias_select->fetch_assoc()): 
                ?>
                    <option value="<?php echo $categoria['id']; ?>" 
                            <?php echo ($categoria_id == $categoria['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <!-- Botón de búsqueda -->
            <button type="submit" class="btn-buscar">
                <i class="fas fa-search"></i>
                Buscar
            </button>
            
            <!-- Botón para limpiar filtros -->
            <?php if (!empty($busqueda) || $categoria_id > 0): ?>
                <a href="<?php echo url('ver-mas-recetas.php'); ?>" class="btn-limpiar">
                    Limpiar
                </a>
            <?php endif; ?>
        </form>
    </div>
</section>

  <!-- === RESULTADOS DE RECETAS === -->
  <section class="recetas-lista">
    <?php if ($recetas->num_rows > 0): ?> <!-- Si hay recetas encontradas -->
      
      <!-- Información sobre los resultados -->
      <div class="resultados-info">
        <p>
          <strong><?php echo $recetas->num_rows; ?></strong> 
          receta<?php echo $recetas->num_rows != 1 ? 's' : ''; ?> encontrada<?php echo $recetas->num_rows != 1 ? 's' : ''; ?>
          
          <?php if (!empty($busqueda)): ?>
            para "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
          <?php endif; ?>
          
          <?php if ($categoria_id > 0): ?>
            <?php 
            $categoria_nombre = '';
            $cat_result = $conexion->query("SELECT nombre FROM categorias WHERE id = $categoria_id");
            if ($cat_result->num_rows > 0) {
                $categoria_nombre = $cat_result->fetch_assoc()['nombre'];
            }
            ?>
            en <strong><?php echo htmlspecialchars($categoria_nombre); ?></strong>
          <?php endif; ?>
        </p>
      </div>

      <!-- Lista en formato grid de las recetas -->
      <div class="recetas-grid">
        <?php while($receta = $recetas->fetch_assoc()): ?>
          <div class="card">
            <!-- Imagen de la receta -->
            <img src="<?php echo asset('img/' . ($receta['imagen'] ?: 'placeholder.jpg')); ?>" 
                 alt="<?php echo htmlspecialchars($receta['titulo']); ?>"
                 onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">
            
            <div class="card-content">
              <span class="categoria-badge"><?php echo htmlspecialchars($receta['categoria_nombre']); ?></span>
              
              <h4><?php echo htmlspecialchars($receta['titulo']); ?></h4>
              
              <!-- Descripción recortada -->
              <p class="descripcion"><?php echo substr(htmlspecialchars($receta['descripcion']), 0, 120); ?>...</p>
              
              <!-- Información adicional -->
              <div class="card-meta">
                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($receta['usuario_nombre']); ?></span>
                <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($receta['fecha_creacion'])); ?></span>
              </div>
              
              <!-- Acciones disponibles -->
              <div class="card-actions">
                <a href="<?php echo url('ver-receta.php?id=' . $receta['id']); ?>" class="btn-ver">
                  <i class="fas fa-eye"></i> Ver Receta
                </a>
                
                <!-- Si el usuario es el dueño de la receta, puede editarla -->
                <?php if (usuarioEstaLogeado() && $_SESSION['usuario_id'] == $receta['usuario_id']): ?>
                  <a href="<?php echo url('editar-receta.php?id=' . $receta['id']); ?>" class="btn-editar">
                    <i class="fas fa-edit"></i> Editar
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

    <?php else: ?> <!-- Si no hay recetas -->
      <div class="no-resultados">
        <i class="fas fa-search" style="font-size: 4rem; color: #f1cadd; margin-bottom: 20px;"></i>
        <h3>No se encontraron recetas</h3>
        <p>
          <?php if (!empty($busqueda)): ?>
            No hay recetas que coincidan con "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
            <?php if ($categoria_id > 0): ?>
              en la categoría seleccionada.
            <?php endif; ?>
          <?php else: ?>
            No hay recetas disponibles en este momento.
          <?php endif; ?>
        </p>
        <div class="no-resultados-actions">
          <a href="<?php echo url('ver-mas-recetas.php'); ?>" class="btn-primary">
            <i class="fas fa-list"></i> Ver Todas las Recetas
          </a>
          <?php if (usuarioEstaLogeado()): ?>
            <a href="<?php echo url('agregar-receta.php'); ?>" class="btn-secondary">
              <i class="fas fa-plus"></i> Crear Primera Receta
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <!-- === PIE DE PÁGINA === -->
  <footer class="footer-simple">
    <p>© 2025 RecepApp — Contacto: <a href="mailto:contacto@recepapp.com">contacto@recepapp.com</a></p>
  </footer>

  <!-- === SCRIPT DE INTERACCIÓN === -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Desplegar menú de perfil
      const profileBtn = document.querySelector('.profile-btn');
      if (profileBtn) {
        profileBtn.addEventListener('click', function() {
          document.querySelector('.profile-dropdown').classList.toggle('show');
        });
      }
      
      // Cerrar menú si se hace clic fuera
      window.addEventListener('click', function(e) {
        if (!e.target.matches('.profile-btn')) {
          const dropdown = document.querySelector('.profile-dropdown');
          if (dropdown && dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
          }
        }
      });

      // Enviar formulario automáticamente al cambiar categoría
      document.getElementById('categoria').addEventListener('change', function() {
        if (this.value !== '0') {
          this.form.submit();
        }
      });

      // Mostrar/ocultar botón "Limpiar" según los filtros activos
      const busquedaInput = document.getElementById('busqueda');
      const categoriaSelect = document.getElementById('categoria');
      
      function toggleClearButton() {
        const clearBtn = document.querySelector('.btn-secondary');
        if (busquedaInput.value || categoriaSelect.value !== '0') {
          if (!clearBtn) {
            const filtroGroup = document.querySelector('.filtro-group:last-child');
            const clearLink = document.createElement('a');
            clearLink.href = '<?php echo url('ver-mas-recetas.php'); ?>';
            clearLink.className = 'btn-secondary';
            clearLink.innerHTML = '<i class="fas fa-times"></i> Limpiar';
            filtroGroup.appendChild(clearLink);
          }
        } else if (clearBtn) {
          clearBtn.remove();
        }
      }
      
      busquedaInput.addEventListener('input', toggleClearButton);
      categoriaSelect.addEventListener('change', toggleClearButton);
    });
  </script>
</body>
</html>
