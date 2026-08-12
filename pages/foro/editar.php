<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Comentario.php';

$usuarioActual = requerirLogin();
$tituloPagina = 'Editar comentario';
$rootPath = '../../';
$error = '';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$comentarioActual = Comentario::buscarPorId($id);

if ($comentarioActual === null) {
    http_response_code(404);
    die('Comentario no encontrado.');
}

$esPropio = (int) $comentarioActual['usuario_id'] === $usuarioActual->getId();
$puedeEditar = ($esPropio && $usuarioActual->tienePermiso('foro:editar_propio'))
    || $usuarioActual->tienePermiso('foro:moderar_todo');

if (!$puedeEditar) {
    http_response_code(403);
    die('Acesso negado: você não pode editar esse comentário.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenido = trim($_POST['contenido'] ?? '');
    if ($contenido === '') {
        $error = 'O comentário não pode estar vazio.';
    } elseif (mb_strlen($contenido) > 2000) {
        $error = 'O comentário é muito longo (máximo 2000 caracteres).';
    } else {
        $comentario = new Comentario($id, (int) $comentarioActual['usuario_id'], $contenido);
        if ($comentario->actualizar()) {
            header('Location: listar.php');
            exit;
        }
        $error = 'Não foi possível salvar as alterações, tente novamente.';
    }
    $comentarioActual['contenido'] = $contenido;
}

require __DIR__ . '/../../includes/header.php';
?>

<section class="seccion seccion--angosta">
    <h1>Editar comentario</h1>

    <?php if ($error !== ''): ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="formulario">
        <input type="hidden" name="id" value="<?= (int) $comentarioActual['id'] ?>">
        <label for="contenido">Comentario</label>
        <textarea id="contenido" name="contenido" rows="4" required maxlength="2000"><?= htmlspecialchars($comentarioActual['contenido']) ?></textarea>
        <button type="submit" class="btn">Guardar cambios</button>
    </form>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>