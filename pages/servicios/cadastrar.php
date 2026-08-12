<?php
require_once __DIR__ .  '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Servicio.php';

$usuarioActual = requerirPermiso('servicios:administrar');
$tituloPagina = 'Cadastrar serviço';
$rootPath = '../../';
$erro = '';

$categorias = Servicio::listarCategorias();
$estadosValidos = ['activo' => 'Disponible', 'mantenimiento' => 'En mantenimiento', 'cerrado' => 'Cerrado'];

$nombre = '';
$descripcion = '';
$categoriaId = null;
$estado = 'activo';
$motivo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoriaId = $_POST['categoria_id'] !== '' ? (int) $_POST['categoria_id'] : null;
    $estado = $_POST['estado'] ?? 'activo';
    $motivo = trim($_POST['motivo'] ?? '');

    if ($nombre === '') {
        $erro = 'El nombre del servicio es obligatorio.';
    } elseif (!array_key_exists($estado, $estadosValidos)) {
        $erro = 'Estado inválido.';
    } else {
        $servicio = new Servicio(null, $nombre, $descripcion !== '' ? $descripcion : null, $categoriaId, $estado, $motivo !== '' ? $motivo : null, $usuarioActual->getId());
        if ($servicio->crear()) {
            header('Location: listar.php');
            exit;
        }
        $erro = 'No se pudo cadastrar el servicio. Intentá de nuevo.';
    }
}

require __DIR__ . '/../../includes/header.php';
?>

<section class="seccion seccion--angosta">
    <h1>Cadastrar serviço</h1>
    <?php if ($erro !== ''): ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post" class="formulario">
        <label for="nombre">Nombre del servicio</label>
        <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($nombre) ?>">

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($descripcion) ?></textarea>

        <label for="categoria_id">Categoría</label>
        <select id="categoria_id" name="categoria_id">
            <option value="">Sin categoría</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>" <?= $categoriaId === (int) $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <?php foreach ($estadosValidos as $valor => $texto): ?>
                <option value="<?= $valor ?>" <?= $estado === $valor ? 'selected' : '' ?>><?= $texto ?></option>
            <?php endforeach; ?>
        </select>

        <label for="motivo">Motivo (si está en mantenimiento o cerrado)</label>
        <input type="text" id="motivo" name="motivo" value="<?= htmlspecialchars($motivo) ?>">

        <button type="submit" class="btn">Cadastrar</button>
    </form>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
