<?php $titulo_pagina = 'Agregar Receta - RecepApp'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="page-header">
  <h2>Agregar Nueva Receta</h2>
</section>

<section class="form-container">
  <div class="auth-form">
    <?php if ($mensaje): ?>
      <div class="<?php echo strpos($mensaje, 'Error') === false ? 'success-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($mensaje); ?>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>Título</label>
        <input type="text" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required>
      </div>

      <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion" rows="3" required><?php echo htmlspecialchars($descripcion); ?></textarea>
      </div>

      <div class="form-group">
        <label>Categoría</label>
        <select name="categoria_id" required>
          <option value="">Selecciona categoría</option>
          <?php foreach ($categorias as $categoria): ?>
            <option value="<?php echo $categoria['id']; ?>"
                    <?php echo ($categoria_id == $categoria['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($categoria['nombre']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Imagen <span style="color: red;">*</span></label>
        <input type="file" name="imagen" accept="image/*" id="imagen" required>
        <small style="color: #666; display: block; margin-top: 5px;">La imagen es obligatoria. Formatos permitidos: JPG, JPEG, PNG, GIF</small>
        <div id="imagen-preview" style="margin-top: 10px; display: none;">
          <img id="preview-img" src="" alt="Vista previa" style="max-width: 300px; max-height: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        </div>
      </div>

      <div class="form-group">
        <label>Ingredientes</label>
        <textarea name="ingredientes" rows="6" required><?php echo htmlspecialchars($ingredientes); ?></textarea>
      </div>

      <div class="form-group">
        <label>Instrucciones</label>
        <textarea name="instrucciones" rows="8" required><?php echo htmlspecialchars($instrucciones); ?></textarea>
      </div>

      <button type="submit" class="btn-primary">Publicar Receta</button>
    </form>
  </div>
</section>

<?php require APP_PATH . '/views/partials/footer.php'; ?>

<script src="<?php echo asset('js/imagen-preview.js'); ?>"></script>
