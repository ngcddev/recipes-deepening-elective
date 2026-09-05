<?php $titulo_pagina = $receta['titulo'] . ' - RecepApp'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="receta-detalle">
  <div class="receta-header">
    <div class="receta-imagen">
      <img src="<?php echo obtenerUrlImagen($receta['imagen']); ?>"
           alt="<?php echo htmlspecialchars($receta['titulo']); ?>"
           onerror="this.src='<?php echo asset('img/placeholder.jpg'); ?>'">
    </div>

    <div class="receta-info">
      <h1><?php echo htmlspecialchars($receta['titulo']); ?></h1>

      <div class="receta-meta">
        <span><i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($receta['categoria_nombre']); ?></span>
        <span><i class="fas fa-user"></i> Por <?php echo htmlspecialchars($receta['usuario_nombre']); ?></span>
        <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($receta['fecha_creacion'])); ?></span>
      </div>

      <div class="receta-desc">
        <?php echo nl2br(htmlspecialchars($receta['descripcion'])); ?>
      </div>

      <?php if (Session::estaLogeado()): ?>
        <form method="POST" class="favorito-form">
          <?php if ($es_favorito): ?>
            <button type="submit" name="accion_favorito" value="eliminar" class="btn-favorito active">
              <i class="fas fa-heart"></i> Quitar de Favoritos
            </button>
          <?php else: ?>
            <button type="submit" name="accion_favorito" value="agregar" class="btn-favorito">
              <i class="far fa-heart"></i> Agregar a Favoritos
            </button>
          <?php endif; ?>
        </form>
      <?php else: ?>
        <div class="login-prompt">
          <a href="<?php echo url('iniciar-sesion.php'); ?>">Inicia sesión</a> para agregar a favoritos
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="receta-content">
    <div class="ingredientes">
      <h3><i class="fas fa-shopping-basket"></i> Ingredientes</h3>
      <div class="ingredientes-lista">
        <?php foreach (explode("\n", $receta['ingredientes']) as $ingrediente): ?>
          <?php if (trim($ingrediente) !== ''): ?>
            <div class="ingrediente-item">
              <i class="fas fa-check-circle"></i>
              <span><?php echo htmlspecialchars(trim($ingrediente)); ?></span>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="instrucciones">
      <h3><i class="fas fa-list-ol"></i> Preparación</h3>
      <div class="instrucciones-lista">
        <?php $paso_num = 1; ?>
        <?php foreach (explode("\n", $receta['instrucciones']) as $instruccion): ?>
          <?php if (trim($instruccion) !== ''): ?>
            <div class="instruccion-item">
              <div class="paso-num"><?php echo $paso_num; ?></div>
              <div class="paso-texto"><?php echo htmlspecialchars(trim($instruccion)); ?></div>
            </div>
            <?php $paso_num++; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <section class="comentarios-section">
    <h3><i class="fas fa-comments"></i> Comentarios</h3>

    <?php if (Session::estaLogeado()): ?>
      <form method="POST" class="comentario-form">
        <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea>
        <button type="submit" class="btn-primary">Publicar Comentario</button>
      </form>
    <?php else: ?>
      <div class="login-prompt">
        <a href="<?php echo url('iniciar-sesion.php'); ?>">Inicia sesión</a> para dejar un comentario
      </div>
    <?php endif; ?>

    <div class="comentarios-lista">
      <?php if (count($comentarios) > 0): ?>
        <?php foreach ($comentarios as $comentario): ?>
          <div class="comentario-item">
            <div class="comentario-header">
              <strong><?php echo htmlspecialchars($comentario['usuario_nombre']); ?></strong>
              <span><?php echo date('d/m/Y H:i', strtotime($comentario['fecha'])); ?></span>

              <?php if (Session::estaLogeado() && Session::usuarioId() == $comentario['usuario_id']): ?>
                <form method="POST" class="eliminar-comentario-form" style="display: inline;">
                  <button type="submit" name="eliminar_comentario" value="<?php echo $comentario['id']; ?>"
                          class="btn-eliminar" onclick="return confirm('¿Eliminar este comentario?')">
                    <i class="fas fa-trash"></i> Eliminar
                  </button>
                </form>
              <?php endif; ?>
            </div>
            <div class="comentario-texto">
              <?php echo nl2br(htmlspecialchars($comentario['comentario'])); ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-comentarios">
          <p>No hay comentarios aún. ¡Sé el primero en comentar!</p>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
