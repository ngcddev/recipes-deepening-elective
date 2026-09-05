<?php $titulo_pagina = 'Todas las Recetas - RecepApp'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="page-header">
  <h2><i class="fas fa-book-open"></i> Todas las Recetas</h2>
  <p>Descubre todas las recetas de nuestra comunidad</p>
</section>

<section class="filtros">
  <div class="filtros-container">
    <form method="GET" class="filtros-form">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" name="busqueda" id="busqueda"
               value="<?php echo htmlspecialchars($busqueda); ?>"
               placeholder="Buscar recetas...">
      </div>

      <select name="categoria" id="categoria">
        <option value="0">Todas las categorías</option>
        <?php foreach ($categorias as $categoria): ?>
          <option value="<?php echo $categoria['id']; ?>"
                  <?php echo ($categoria_id == $categoria['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($categoria['nombre']); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="btn-buscar">
        <i class="fas fa-search"></i> Buscar
      </button>

      <?php if ($busqueda !== '' || $categoria_id > 0): ?>
        <a href="<?php echo url('ver-mas-recetas.php'); ?>" class="btn-limpiar">Limpiar</a>
      <?php endif; ?>
    </form>
  </div>
</section>

<section class="recetas-lista">
  <?php if (count($recetas) > 0): ?>
    <div class="resultados-info">
      <p>
        <strong><?php echo count($recetas); ?></strong>
        receta<?php echo count($recetas) != 1 ? 's' : ''; ?> encontrada<?php echo count($recetas) != 1 ? 's' : ''; ?>
        <?php if ($busqueda !== ''): ?>
          para "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
        <?php endif; ?>
        <?php if ($categoria_id > 0): ?>
          en <strong><?php echo htmlspecialchars($categoria_nombre); ?></strong>
        <?php endif; ?>
      </p>
    </div>

    <div class="recetas-grid">
      <?php foreach ($recetas as $receta): ?>
        <div class="card">
          <img src="<?php echo obtenerUrlImagen($receta['imagen']); ?>"
               alt="<?php echo htmlspecialchars($receta['titulo']); ?>"
               onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">

          <div class="card-content">
            <span class="categoria-badge"><?php echo htmlspecialchars($receta['categoria_nombre']); ?></span>
            <h4><?php echo htmlspecialchars($receta['titulo']); ?></h4>
            <p class="descripcion"><?php echo substr(htmlspecialchars($receta['descripcion']), 0, 120); ?>...</p>

            <div class="card-meta">
              <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($receta['usuario_nombre']); ?></span>
              <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($receta['fecha_creacion'])); ?></span>
            </div>

            <div class="card-actions">
              <a href="<?php echo url('ver-receta.php?id=' . $receta['id']); ?>" class="btn-ver">
                <i class="fas fa-eye"></i> Ver Receta
              </a>
              <?php if (Session::estaLogeado() && Session::usuarioId() == $receta['usuario_id']): ?>
                <a href="<?php echo url('editar-receta.php?id=' . $receta['id']); ?>" class="btn-editar">
                  <i class="fas fa-edit"></i> Editar
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  <?php else: ?>
    <div class="no-resultados">
      <i class="fas fa-search" style="font-size: 4rem; color: #f1cadd; margin-bottom: 20px;"></i>
      <h3>No se encontraron recetas</h3>
      <p>
        <?php if ($busqueda !== ''): ?>
          No hay recetas que coincidan con "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
          <?php if ($categoria_id > 0): ?> en la categoría seleccionada.<?php endif; ?>
        <?php else: ?>
          No hay recetas disponibles en este momento.
        <?php endif; ?>
      </p>
      <div class="no-resultados-actions">
        <a href="<?php echo url('ver-mas-recetas.php'); ?>" class="btn-primary">
          <i class="fas fa-list"></i> Ver Todas las Recetas
        </a>
        <?php if (Session::estaLogeado()): ?>
          <a href="<?php echo url('agregar-receta.php'); ?>" class="btn-secondary">
            <i class="fas fa-plus"></i> Crear Primera Receta
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</section>

<?php require APP_PATH . '/views/partials/footer.php'; ?>

<script src="<?php echo asset('js/filtros-recetas.js'); ?>"></script>
