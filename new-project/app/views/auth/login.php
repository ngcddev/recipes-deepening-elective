<?php $titulo_pagina = 'Iniciar Sesión - RecepApp'; ?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<section class="auth-container">
  <div class="auth-form">
    <h2>Iniciar Sesión</h2>

    <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
      <div class="success-message">¡Registro exitoso! Ahora puedes iniciar sesión.</div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <?php echo csrf_field(); ?>

      <div class="form-group">
        <label>Correo Electrónico:</label>
        <input type="email" name="correo" required>
      </div>

      <div class="form-group">
        <label>Contraseña:</label>
        <input type="password" name="contraseña" required>
      </div>

      <button type="submit" class="btn-primary">Iniciar Sesión</button>
    </form>

    <p>¿No tienes cuenta? <a href="<?php echo route('AuthController', 'register'); ?>">Regístrate aquí</a></p>
    <a href="<?php echo route('HomeController', 'index'); ?>" class="btn-secondary">← Volver al Inicio</a>
  </div>
</section>

<?php require APP_PATH . '/views/partials/footer.php'; ?>