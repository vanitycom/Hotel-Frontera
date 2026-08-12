<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Comentario.php';

$usuarioActual = requerirLogin(); //algo muito imporante aqui, é que só dá para ver ou escrever no fórum se tu estiver logado
$tituloPagina = 'Foro de huéspedes';
$rootPath = '../../';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenido'])) {
    $contenido = trim($_POST['contenido']);
    if ($contenido === '') {
        $error = 'El comentario no puede estar vacío.';
    } elseif (mb_strlen($contenido) > 2000) {
        $error = 'El comentario es demasiado largo (máximo 2000 caracteres).';
    } else {
        $comentario = new Comentario(null, $usuarioActual->getId(), $contenido);
        if (!$comentario->crear()) {
            $error = 'No se pudo publicar el comentario. Intentá de nuevo.';
        }
    }
}

$comentarios = Comentario::listarTodos();

require __DIR__ . '/../../includes/header.php';
?>

<section class="seccion seccion--angosta">
    <h1>Foro de la comunidad</h1>
    <p class="texto-ayuda">Charlá con otros huéspedes del hotel. Mantené el respeto en tus mensajes.</p>

    <?php if ($error !== ''): ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="formulario formulario--foro">
        <label for="contenido">Nuevo comentario</label>
        <textarea id="contenido" name="contenido" rows="3" required maxlength="2000"></textarea>
        <button type="submit" class="btn">Publicar</button>
    </form>

    <ul class="lista-comentarios">
        <?php foreach ($comentarios as $c): ?>
            <?php
                $esPropio = (int) $c['usuario_id'] === $usuarioActual->getId();
                $puedeEditar = $esPropio && $usuarioActual->tienePermiso('foro:editar_propio');
                $puedeBorrar = ($esPropio && $usuarioActual->tienePermiso('foro:borrar_propio'))
                    || $usuarioActual->tienePermiso('foro:moderar_todo');
                $puedeEditarOtro = $usuarioActual->tienePermiso('foro:moderar_todo') && !$esPropio;
                $esFijado = !empty($c['fijado']);
                $esEditado = !empty($c['editado']);
            ?>
            <li class="comentario <?= $esFijado ? 'comentario--fijado' : '' ?>">
                <div class="comentario__cabecera">
                    <strong><?= htmlspecialchars($c['autor_nombre']) ?></strong>
                    <span class="badge badge--rol"><?= htmlspecialchars($c['autor_rol']) ?></span>
                    <time><?= htmlspecialchars($c['fecha']) ?></time>
                    <?php if ($esEditado): ?><small>(editado)</small><?php endif; ?>
                </div>
                <p class="comentario__contenido"><?= nl2br(htmlspecialchars($c['contenido'])) ?></p>

                <?php if ($puedeEditar || $puedeEditarOtro || $puedeBorrar): ?>
                    <div class="comentario__acciones">
                        <?php if ($puedeEditar || $puedeEditarOtro): ?>
                            <a href="editar.php?id=<?= (int) $c['id'] ?>">Editar</a>
                        <?php endif; ?>
                        <?php if ($puedeBorrar): ?>
                            <form method="post" action="excluir.php" onsubmit="return confirm('¿Eliminar este comentario?');" class="form-inline">
                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                <button type="submit" class="link-boton">Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>

        <?php if (empty($comentarios)): ?>
            <li>Todavía no hay comentarios. ¡Sé el primero en escribir!</li>
        <?php endif; ?>
    </ul>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
