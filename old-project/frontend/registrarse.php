<?php
// ===========================================================
//  BLOQUE PHP: PROCESO DE REGISTRO DE USUARIO EN RECEPAPP
// ===========================================================

// Se incluyen los archivos necesarios para la sesión, conexión y funciones auxiliares.
require_once __DIR__ . '/../backend/session.php';   // Maneja las sesiones (login, logout, etc.)
require_once __DIR__ . '/../backend/conexion.php';  // Conexión a la base de datos
require_once '../backend/helpers.php';              // Funciones de ayuda (como url(), asset(), etc.)

// -----------------------------------------------------------
//  VALIDACIÓN DE LOS DATOS RECIBIDOS DESDE EL FORMULARIO
// -----------------------------------------------------------

// Se verifica si el formulario fue enviado mediante el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Se obtienen los valores enviados por el usuario desde el formulario HTML
    $nombre = trim($_POST['nombre']);       // Se eliminan espacios antes y después del nombre
    $correo = trim($_POST['correo']);       // Se elimina el espacio del correo
    $contraseña = $_POST['contraseña'];     // Contraseña tal como se ingresó

    // ----------------------------------------------
    //  Verificar si ya existe un usuario con ese correo
    // ----------------------------------------------
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);    // Se asocia el valor del correo al parámetro
    $stmt->execute();                   // Se ejecuta la consulta
    $stmt->store_result();              // Se almacenan los resultados para poder contarlos

    // Si ya existe un usuario con ese correo, se muestra mensaje de error
    if ($stmt->num_rows > 0) {
        $error = "Este correo ya está registrado";
    } else {
        // ----------------------------------------------
        //  CREACIÓN DEL NUEVO USUARIO
        // ----------------------------------------------

        // Se cifra la contraseña antes de guardarla (por seguridad)
        $hash_contraseña = password_hash($contraseña, PASSWORD_DEFAULT);

        // Se prepara la consulta SQL para insertar el nuevo registro
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $correo, $hash_contraseña); // Vincula los valores

        // Si la consulta se ejecuta correctamente, se redirige al login
        if ($stmt->execute()) {
            header("Location: iniciar-sesion.php?registro=exitoso"); // Redirecciona con mensaje de éxito
            exit(); // Detiene la ejecución del script
        } else {
            // Si algo falla al insertar el registro, muestra error
            $error = "Error al registrar usuario";
        }
    }

    // Se cierra la consulta preparada
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Configuración básica del documento -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Título que aparece en la pestaña del navegador -->
  <title>Registrarse - RecepApp</title>

  <!-- Enlace al archivo CSS principal -->
  <link rel="stylesheet" href="css/style.css" />

  <!-- Fuente externa desde Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
</head>

<body>
<!-- ===================================================== -->
<!-- NAVBAR (barra superior con logo y menú de navegación) -->
<!-- ===================================================== -->
<header class="navbar">
  <div class="logo">
    <!-- Ícono y nombre del sitio -->
    <i class="fas fa-utensils"></i>
    <h1>RecepApp</h1>
  </div>

  <!-- Menú principal -->
  <nav class="menu">
    <a href="<?php echo url('index.php'); ?>">Inicio</a>
    <a href="<?php echo url('ver-mas-recetas.php'); ?>">Recetas</a>

    <!-- Si el usuario está logeado, muestra opciones de perfil -->
    <?php if (usuarioEstaLogeado()): ?>
      <span class="user">Hola, <?php echo htmlspecialchars(obtenerUsuarioNombre()); ?></span>
      <div class="profile-menu">
        <button class="profile-btn"><i class="fas fa-user"></i> Mi Perfil</button>
        
        <!-- Menú desplegable del perfil -->
        <div class="profile-dropdown">
          <a href="<?php echo url('ver-recetas-propias.php'); ?>"><i class="fas fa-book"></i> Mis Recetas</a>
          <a href="<?php echo url('ver-favoritos.php'); ?>"><i class="fas fa-heart"></i> Favoritos</a>
          <a href="<?php echo url('../backend/logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
      </div>
    <?php else: ?>
      <!-- Si no está logeado, muestra los enlaces de inicio y registro -->
      <a href="<?php echo url('iniciar-sesion.php'); ?>">Iniciar Sesión</a>
      <a href="<?php echo url('registrarse.php'); ?>">Registrarse</a>
    <?php endif; ?>
  </nav>
</header>

<!-- ===================================================== -->
<!-- SECCIÓN DEL FORMULARIO DE REGISTRO DE USUARIO -->
<!-- ===================================================== -->
<section class="auth-container">
  <div class="auth-form">
    <h2>Crear Cuenta</h2>

    <!-- Si existe un error (correo duplicado, etc.), se muestra el mensaje -->
    <?php if (isset($error)): ?>
      <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <!-- Formulario de registro -->
    <form method="POST">
      <!-- Campo: Nombre completo -->
      <div class="form-group">
        <label>Nombre Completo:</label>
        <input type="text" name="nombre" required>
      </div>
      
      <!-- Campo: Correo -->
      <div class="form-group">
        <label>Correo Electrónico:</label>
        <input type="email" name="correo" required>
      </div>
      
      <!-- Campo: Contraseña -->
      <div class="form-group">
        <label>Contraseña:</label>
        <input type="password" name="contraseña" required minlength="6">
      </div>
      
      <!-- Botón para enviar el formulario -->
      <button type="submit" class="btn-primary">Registrarse</button>
    </form>
    
    <!-- Enlace para ir al inicio de sesión -->
    <p>¿Ya tienes cuenta? <a href="iniciar-sesion.php">Inicia Sesión aquí</a></p>

    <!-- Botón para volver al inicio -->
    <a href="index.php" class="btn-secondary">← Volver al Inicio</a>
  </div>
</section>
</body>
</html>
