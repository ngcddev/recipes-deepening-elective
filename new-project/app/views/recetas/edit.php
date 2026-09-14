<?php $titulo_pagina = 'Editar Receta - RecepApp'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="page-header">
  <h2><i class="fas fa-edit"></i> Editar Receta</h2>
  <p>Modifica los detalles de tu receta</p>
</section>

<section class="form-container">
  <div class="auth-form">
    <?php if ($mensaje): ?>
      <div class="<?php echo strpos($mensaje, 'Error') === false ? 'success-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($mensaje); ?>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>

      <div class="form-group">
        <label for="titulo"><i class="fas fa-heading"></i> Título de la Receta</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($receta['titulo']); ?>" required>
      </div>

      <div class="form-group">
        <label for="descripcion"><i class="fas fa-align-left"></i> Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($receta['descripcion']); ?></textarea>
      </div>

      <div class="form-group">
        <label for="categoria_id"><i class="fas fa-layer-group"></i> Categoría</label>
        <select id="categoria_id" name="categoria_id" required>
          <option value="">Selecciona una categoría</option>
          <?php foreach ($categorias as $categoria): ?>
            <option value="<?php echo $categoria['id']; ?>"
                    <?php echo ($receta['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($categoria['nombre']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="imagen"><i class="fas fa-image"></i> Imagen de la Receta</label>
        <?php if (!empty($receta['imagen'])): ?>
          <div style="margin-bottom: 10px;">
            <img src="<?php echo obtenerUrlImagen($receta['imagen']); ?>" alt="Imagen actual" style="max-width: 200px; border-radius: 8px;" id="current-image">
            <p><small>Imagen actual</small></p>
          </div>
        <?php endif; ?>
        <input type="file" id="imagen" name="imagen" accept="image/*">
        <small>Deja vacío para mantener la imagen actual. Formatos: JPG, PNG, GIF</small>
        <div id="imagen-preview" style="margin-top: 10px; display: none;">
          <img id="preview-img" src="" alt="Vista previa" style="max-width: 300px; max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          <p><small>Vista previa de la nueva imagen</small></p>
        </div>
      </div>

      <div class="form-group">
        <label for="ingredientes"><i class="fas fa-shopping-basket"></i> Ingredientes</label>
        <textarea id="ingredientes" name="ingredientes" rows="6" required><?php echo htmlspecialchars($receta['ingredientes']); ?></textarea>
        <small>Un ingrediente por línea</small>
      </div>

      <div class="form-group">
        <label for="instrucciones"><i class="fas fa-list-ol"></i> Instrucciones</label>
        <textarea id="instrucciones" name="instrucciones" rows="8" required><?php echo htmlspecialchars($receta['instrucciones']); ?></textarea>
        <small>Un paso por línea</small>
      </div>

      <button type="submit" class="btn-primary">
        <i class="fas fa-save"></i> Guardar Cambios
      </button>

      <a href="<?php echo route('RecetaController', 'mine'); ?>" class="btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver a Mis Recetas
      </a>

      <a href="<?php echo route('RecetaController', 'show', ['id' => $receta['id']]); ?>" class="btn-secondary">
        <i class="fas fa-eye"></i> Ver Receta
      </a>
    </form>
  </div>
</section>

<?php require APP_PATH . '/views/partials/footer.php'; ?>

<script src="<?php echo asset('js/imagen-preview.js'); ?>"></script>