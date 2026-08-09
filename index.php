<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/classes/Servicio.php';

$tituloPagina = 'Inicio';
$rootPath = '';
$servicios = Servicio::listarTodos();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <h1>Bienvenido al Hotel Frontera</h1>
    <p>Un espacio pensado para el descanso y la comunidad. Piscina climatizada, spa, gimnasio,
       restaurante con vista al jardín y salones de eventos, todo en un mismo lugar.</p>
    <?php if (usuarioLogueado() === null): ?>
        <a href="register.php" class="btn">Sumate a la comunidad del hotel</a>
    <?php endif; ?>
</section>

<section class="seccion">
    <h2>Estado actual de zonas y servicios</h2>
    <p class="texto-ayuda">Antes de tu visita, revisá qué zonas están disponibles.</p>

    <div class="grid-servicios">
        <?php foreach ($servicios as $s): ?>
            <article class="tarjeta-servicio tarjeta-servicio--<?= htmlspecialchars($s['estado']) ?>">
                <h3><?= htmlspecialchars($s['nombre']) ?></h3>
                <span class="badge badge--<?= htmlspecialchars($s['estado']) ?>">
                    <?= ucfirst(htmlspecialchars($s['estado'])) ?>
                </span>
                <?php if (!empty($s['descripcion'])): ?>
                    <p><?= htmlspecialchars($s['descripcion']) ?></p>
                <?php endif; ?>
                <?php if ($s['estado'] !== 'activo' && !empty($s['motivo'])): ?>
                    <p class="motivo">Motivo: <?= htmlspecialchars($s['motivo']) ?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>

        <?php if (empty($servicios)): ?>
            <p>Todavía no hay servicios cargados.</p>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>