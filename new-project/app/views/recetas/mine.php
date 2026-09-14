<?php $titulo_pagina = 'Mis Recetas - RecepApp'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="page-header">
  <h2><i class="fas fa-book"></i> Mis Recetas</h2>
  <p>Gestiona todas las recetas que has creado</p>
  <a href="<?php echo route('RecetaController', 'create'); ?>" class="btn-primary">
    <i class="fas fa-plus"></i> Agregar Nueva Receta
  </a>
</section>

<section class="recetas-lista">
  <?php if (!empty($mensaje)): ?>
    <div class="<?php echo strpos($mensaje, '❌') === false ? 'success-message' : 'error-message'; ?>">
      <?php echo htmlspecialchars($mensaje); ?>
    </div>
  <?php endif; ?>

  <?php if (count($recetas) > 0): ?>
    <div class="recetas-grid">
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
                <i class="fas fa-eye"></i> Ver
              </a>
              <a href="<?php echo route('RecetaController', 'edit', ['id' => $receta['id']]); ?>" class="btn-editar">
                <i class="fas fa-edit"></i> Editar
              </a>
              <form method="POST" class="eliminar-receta-form">
                <?php echo csrf_field(); ?>
                <button type="submit" name="eliminar_receta" value="<?php echo $receta['id']; ?>"
                        class="btn-eliminar"
                        onclick="return confirm('¿Estás seguro de eliminar esta receta? Esta acción no se puede deshacer.')">
                  <i class="fas fa-trash"></i> Eliminar
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="no-resultados">
      <i class="fas fa-book" style="font-size: 4rem; color: #f1cadd; margin-bottom: 20px;"></i>
      <h3>No has creado ninguna receta aún</h3>
      <p>Comparte tus recetas favoritas con la comunidad de RecepApp.</p>
    </div>
  <?php endif; ?>
</section>

<?php require APP_PATH . '/views/partials/footer.php'; ?>