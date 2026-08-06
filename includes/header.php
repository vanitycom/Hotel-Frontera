<?php
$rootPath = $rootPath ?? '';
$usuarioActual = usuarioLogueado();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tituloPagina) ? htmlspecialchars($tituloPagina) . ' · ' : '' ?>Hotel Frontera</title>
    <link rel="stylesheet" href="<?= $rootPath ?>css/style.css">
</head>
<body>
<header class="site-header">
    <div class="site-header__brand">
        <a href="<?= $rootPath ?>index.php"> Hotel Frontera</a>
    </div>
    <nav class="site-nav">
        <a href="<?= $rootPath ?>index.php">Inicio</a>
        <a href="<?= $rootPath ?>pages/servicios/listar.php">Zonas y servicios</a>
        <?php if ($usuarioActual !== null): ?>
            <a href="<?= $rootPath ?>pages/foro/listar.php">Foro</a>
            <?php if ($usuarioActual->tienePermiso('usuarios:administrar')): ?>
                <a href="<?= $rootPath ?>pages/usuarios/listar.php">Usuarios</a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
    <div class="site-header__session">
        <?php if ($usuarioActual !== null): ?>
            <span>Hola, <?= htmlspecialchars($usuarioActual->getNombre()) ?> <small>(<?= htmlspecialchars($usuarioActual->getRol()) ?>)</small></span>
            <a href="<?= $rootPath ?>logout.php" class="btn btn--small">Cerrar sesión</a>
        <?php else: ?>
            <a href="<?= $rootPath ?>login.php" class="btn btn--small">Iniciar sesión</a>
            <a href="<?= $rootPath ?>register.php" class="btn btn--small btn--outline">Registrarme</a>
        <?php endif; ?>
    </div>
</header>
<main class="site-main">
