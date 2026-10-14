<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="recetas-lista">
  <div class="no-resultados">
    <i class="fas fa-exclamation-triangle"></i>
    <h3>Error <?php echo $codigo; ?></h3>
    <p><?php echo htmlspecialchars($mensaje); ?></p>
    <div class="no-resultados-actions">
      <a href="<?php echo route('HomeController', 'index'); ?>" class="btn-primary">
        <i class="fas fa-home"></i> Volver al inicio
      </a>
      <a href="<?php echo route('RecetaController', 'index'); ?>" class="btn-secondary">
        <i class="fas fa-book-open"></i> Ver recetas
      </a>
    </div>
  </div>
</section>

<?php require APP_PATH . '/views/partials/footer.php'; ?>