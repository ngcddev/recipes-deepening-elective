<?php $titulo_pagina = 'Mis Favoritos - RecepApp'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="page-header">
  <h2><i class="fas fa-heart"></i> Mis Recetas Favoritas</h2>
  <p>Aquí encontrarás todas las recetas que has guardado como favoritas</p>
</section>

<section class="recetas-lista">
  <?php if (count($favoritos) > 0): ?>
    <div class="recetas-grid">
      <?php foreach ($favoritos as $receta): ?>
        <div class="card">
          <img src="<?php echo obtenerUrlImagen($receta['imagen']); ?>"
               alt="<?php echo htmlspecialchars($receta['titulo']); ?>"
               onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">

          <div class="card-content">
            <span class="categoria-badge"><?php echo htmlspecialchars($receta['categoria_nombre']); ?></span>
            <h4><?php echo htmlspecialchars($receta['titulo']); ?></h4>
            <p class="descripcion"><?php echo substr(htmlspecialchars($receta['descripcion']), 0, 100); ?>...</p>

            <div class="card-meta">
              <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($receta['usuario_nombre']); ?></span>
              <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($receta['fecha_creacion'])); ?></span>
            </div>

            <div class="card-actions">
              <a href="<?php echo url('ver-receta.php?id=' . $receta['id']); ?>" class="btn-ver">
                <i class="fas fa-eye"></i> Ver Receta
              </a>
              <form method="POST" class="eliminar-favorito-form">
                <button type="submit" name="eliminar_favorito" value="<?php echo $receta['id']; ?>"
                        class="btn-eliminar"
                        onclick="return confirm('¿Quitar de favoritos?')">
                  <i class="fas fa-heart-broken"></i> Quitar
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
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

<?php require APP_PATH . '/views/partials/footer.php'; ?>
