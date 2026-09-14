<?php $titulo_pagina = 'Cocina Fácil'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="hero">
  <h2>Descubre tu próxima receta favorita </h2>
  <p>Busca entre cientos de recetas fáciles y deliciosas. Filtra por categoría o guarda tus favoritas.</p>
  <form action="<?php echo route('RecetaController', 'index'); ?>" method="GET" class="search-container">
    <input type="hidden" name="controller" value="RecetaController">
    <input type="hidden" name="action" value="index">
    <input type="text" name="busqueda" placeholder="Buscar recetas..." />
    <button type="submit"><i class="fas fa-search"></i> Buscar</button>
  </form>
</section>

<section class="categorias">
  <h3>Categorías Populares</h3>
  <div class="cat-grid">
    <?php foreach ($categorias as $categoria): ?>
      <?php $imagen_categoria = $imagenes_categorias[$categoria['nombre']] ?? 'placeholder.jpg'; ?>
      <a href="<?php echo route('RecetaController', 'index', ['categoria' => $categoria['id']]); ?>" class="cat-card-link">
        <div class="cat-card">
          <img src="<?php echo asset('img/' . $imagen_categoria); ?>"
               alt="<?php echo htmlspecialchars($categoria['nombre']); ?>"
               onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">
          <h4><?php echo htmlspecialchars($categoria['nombre']); ?></h4>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="recetas-destacadas">
  <h3>Recetas Destacadas</h3>
  <div class="recetas-grid">
    <?php if (count($recetas) > 0): ?>
      <?php foreach ($recetas as $receta): ?>
        <div class="card">
          <img src="<?php echo obtenerUrlImagen($receta['imagen']); ?>"
               alt="<?php echo htmlspecialchars($receta['titulo']); ?>"
               onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">
          <div class="card-content">
            <span class="categoria-badge"><?php echo htmlspecialchars($receta['categoria_nombre']); ?></span>
            <h4><?php echo htmlspecialchars($receta['titulo']); ?></h4>
            <p class="descripcion"><?php echo substr(htmlspecialchars($receta['descripcion']), 0, 100); ?>...</p>
            <div class="card-meta">
              <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($receta['fecha_creacion'])); ?></span>
            </div>
            <div class="card-actions">
              <a href="<?php echo route('RecetaController', 'show', ['id' => $receta['id']]); ?>" class="btn-ver">
                <i class="fas fa-eye"></i> Ver Receta
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No hay recetas disponibles aún.</p>
    <?php endif; ?>
  </div>
</section>

<?php require APP_PATH . '/views/partials/footer.php'; ?>