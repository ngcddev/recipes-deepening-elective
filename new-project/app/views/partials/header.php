<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $titulo_pagina ?? 'RecepApp'; ?></title>
  <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a2d9d6cfd5.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="navbar">
  <div class="logo">
    <i class="fas fa-utensils"></i>
    <h1>RecepApp</h1>
  </div>
  <nav class="menu">
    <a href="<?php echo url('index.php'); ?>">Inicio</a>
    <a href="<?php echo url('ver-mas-recetas.php'); ?>">Recetas</a>
    <?php if (Session::estaLogeado()): ?>
      <span class="user">Hola, <?php echo htmlspecialchars(Session::usuarioNombre()); ?></span>
      <div class="profile-menu">
        <button class="profile-btn"><i class="fas fa-user"></i> Mi Perfil</button>
        <div class="profile-dropdown">
          <a href="<?php echo url('ver-recetas-propias.php'); ?>"><i class="fas fa-book"></i> Mis Recetas</a>
          <a href="<?php echo url('ver-favoritos.php'); ?>"><i class="fas fa-heart"></i> Favoritos</a>
          <a href="<?php echo url('logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
      </div>
    <?php else: ?>
      <a href="<?php echo url('iniciar-sesion.php'); ?>">Iniciar Sesión</a>
      <a href="<?php echo url('registrarse.php'); ?>">Registrarse</a>
    <?php endif; ?>
  </nav>
</header>
