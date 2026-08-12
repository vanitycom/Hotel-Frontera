<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Conexion.php';

$usuarioActual = requerirPermiso('usuarios:administrar');
$tituloPagina = 'Usuarios';
$rootPath = '../../';

$pdo = Conexion::getInstancia();
$usuarios = $pdo->query('SELECT idUsuario AS id, nome AS nombre, email, tipoDeUsuario AS rol FROM usuarios ORDER BY nome ASC')->fetchAll();

foreach ($usuarios as $pessoas){
    $pessoas['rol'] = Usuario::rolDesdeBd($pessoas['rol']);
}
unset($pessoas);

require __DIR__ . '/../../includes/header.php';
?>

<section class="seccion">
    <h1>Usuarios del sistema</h1>
    <p class="texto-ayuda">Los huéspedes se registran solos desde "Registrarme". Para dar de alta funcionarios o
        dueños, cambiá el rol de un usuario existente desde "Editar".</p>

    <table class="tabla">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge badge--rol"><?= htmlspecialchars($u['rol']) ?></span></td>
                <td class="acciones">
                    <a href="editar.php?id=<?= (int) $u['id'] ?>">Editar</a>
                    <?php if ((int) $u['id'] !== $usuarioActual->getId()): ?>
                        <form method="post" action="excluir.php" onsubmit="return confirm('Deseja deletar este usuário? Os comentários no fórum também serão apagados.');" class="form-inline">
                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                            <button type="submit" class="link-boton">Eliminar</button>
                        </form>
                    <?php else: ?>
                        <small>(vos)</small>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
