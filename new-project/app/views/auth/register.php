<?php $titulo_pagina = 'Registrarse - RecepApp'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="auth-container">
  <div class="auth-form">
    <h2>Crear Cuenta</h2>

    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Nombre Completo:</label>
        <input type="text" name="nombre" required>
      </div>

      <div class="form-group">
        <label>Correo Electrónico:</label>
        <input type="email" name="correo" required>
      </div>

      <div class="form-group">
        <label>Contraseña:</label>
        <input type="password" name="contraseña" required minlength="6">
      </div>

      <button type="submit" class="btn-primary">Registrarse</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="<?php echo url('iniciar-sesion.php'); ?>">Inicia Sesión aquí</a></p>
    <a href="<?php echo url('index.php'); ?>" class="btn-secondary">← Volver al Inicio</a>
  </div>
</section>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
