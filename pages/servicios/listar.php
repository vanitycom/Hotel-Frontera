<?php
require_once __DIR__ .  '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Servicio.php';

$usuarioActual = requerirPermiso('servicios:administrar');
$tituloPagina = 'Zonas y Servicios';
$rootPath = '../../';

$podeAdministrar = $usuarioActual->tienePermiso('servicios:administrar');
$servicios = Servicio::listarTodos();

require __DIR__ . '/../../includes/header.php';
?>

<section class="seccion">
    <div class="cabecera-seccion">
        <h1>Zonas e Serviços do Hotel</h1>
        <?php if ($podeAdministrar) : ?>
            <a href="cadastrar.php" class="btn">Cadastrar novo serviço</a>
        <?php endif; ?>
    </div>

    <table class="tabla">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Estado</th>
                <th>Descrição / Motivo</th>
                <?php if ($podeAdministrar) : ?>
                    <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios as $s): ?>
                <?php
                    $id = $s['id'] ?? 0;
                    $nombre = $s['nombre'] ?? '';
                    $categoria = $s['categoria_nombre'] ?? 'Sin categoría';
                    $estado = $s['estado'] ?? 'activo';
                    $descripcion = $s['descripcion'] ?? '';
                    $motivo = $s['motivo'] ?? '';
                ?>
                <tr>
                    <td><?= htmlspecialchars($nombre) ?></td>
                    <td><?= htmlspecialchars($categoria) ?></td>
                    <td>
                        <span class="badge badge--<?= htmlspecialchars($estado) ?>">
                            <?= ucfirst(htmlspecialchars($estado))?>
                        </span>
                    </td>
                    <td>
                        <?= htmlspecialchars($descripcion) ?>
                        <?php if ($estado !== 'activo' && !empty($motivo)): ?>
                            <br><small class="texto-ayuda"><strong>Motivo:</strong> <?= htmlspecialchars($motivo) ?></small>
                            <?php endif; ?>
                        </td>
                    <?php if ($podeAdministrar): ?>
                        <td class="acciones">
                            <a href="editar.php?id=<?= (int) $id ?>">Editar</a>
                            <form method="post" action="excluir.php" onsubmit="return confirm('Deseja excluir esse serviço?');" class="form-inline">
                                <input type="hidden" name="id" value="<?= (int) $id ?>">
                                <button type="submit" class="link-boton">Eliminar</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

            <?php if(empty($servicios)): ?>
                <tr>
                    <td colspan="<?= $podeAdministrar ? '4' : '3' ?>">Não há serviços registrados no momento.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>