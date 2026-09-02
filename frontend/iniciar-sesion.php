<?php
// Importa el archivo de conexión a la base de datos
require_once __DIR__ . '/../backend/conexion.php';

// Importa el archivo de gestión de sesiones (para saber si un usuario está logueado o no)
require_once __DIR__ . '/../backend/session.php';

// Si el usuario ya inició sesión, lo redirige al inicio (no puede volver a la página de login)
if (usuarioEstaLogeado()) {
    header("Location: index.php");
    exit(); // Finaliza la ejecución del script
}

// Si el formulario fue enviado por método POST (el usuario presionó el botón de iniciar sesión)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene los datos del formulario y elimina espacios en blanco del correo
    $correo = trim($_POST['correo']);
    $contraseña = $_POST['contraseña'];
    
    // Prepara una consulta segura (evita inyección SQL)
    $stmt = $conexion->prepare("SELECT id, nombre, contraseña FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo); // Vincula el parámetro del correo al placeholder
    $stmt->execute(); // Ejecuta la consulta
    $stmt->store_result(); // Almacena los resultados para poder contar filas
    
    // Si el usuario existe (hay 1 fila con ese correo)
    if ($stmt->num_rows === 1) {
        // Asigna los resultados a variables
        $stmt->bind_result($id, $nombre, $hash_contraseña);
        $stmt->fetch();
        
        // Verifica si la contraseña ingresada coincide con el hash almacenado en la base de datos
        if (password_verify($contraseña, $hash_contraseña)) {
            // Si la contraseña es correcta, guarda datos del usuario en la sesión
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nombre'] = $nombre;

            // Redirige al usuario a la página principal
            header("Location: index.php");
            exit();
        } else {
            // Si la contraseña no coincide, muestra mensaje de error
            $error = "Contraseña incorrecta";
        }
    } else {
        // Si no se encuentra un usuario con ese correo
        $error = "Usuario no encontrado";
    }

    // Cierra la consulta preparada
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Configuración básica del documento -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión - RecepApp</title>

  <!-- Archivo CSS principal -->
  <link rel="stylesheet" href="css/style.css" />

  <!-- Fuente para estilos de texto -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
</head>
<body>
  <!-- Barra de navegación superior -->
  <header class="navbar">
    <div class="logo">
      <i class="fas fa-utensils"></i>
      <h1>RecepApp</h1>
    </div>
    <nav class="menu">
      
      <a href="index.php">Inicio</a>
      <a href="ver-mas-recetas.php">Recetas</a>
      <a href="login.php" class="active">Iniciar Sesión</a>
      <a href="registrarse.php">Registrarse</a>
    </nav>
  </header>

  <!-- Contenedor principal del formulario de inicio de sesión -->
  <section class="auth-container">
    <div class="auth-form">
      <h2>Iniciar Sesión</h2>

      <!-- Mensaje que aparece si el usuario se registró correctamente -->
      <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
        <div class="success-message">¡Registro exitoso! Ahora puedes iniciar sesión.</div>
      <?php endif; ?>
      
      <!-- Muestra mensajes de error (usuario no encontrado o contraseña incorrecta) -->
      <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>
      
      <!-- Formulario de inicio de sesión -->
      <form method="POST">
        <div class="form-group">
          <label>Correo Electrónico:</label>
          <input type="email" name="correo" required>
        </div>
        
        <div class="form-group">
          <label>Contraseña:</label>
          <input type="password" name="contraseña" required>
        </div>
        
        <!-- Botón de envío -->
        <button type="submit" class="btn-primary">Iniciar Sesión</button>
      </form>
      
      <!-- Enlace para registrarse si no tiene cuenta -->
      <p>¿No tienes cuenta? <a href="registrarse.php">Regístrate aquí</a></p>

      <!-- Botón para volver al inicio -->
      <a href="index.php" class="btn-secondary">← Volver al Inicio</a>
    </div>
  </section>
</body>
</html>