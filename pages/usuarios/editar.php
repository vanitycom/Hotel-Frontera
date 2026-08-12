<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Conexion.php';

$usuarioActual = requerirPermiso('usuarios:administrar');
$tituloPagina = 'Editar usuário';
$rootPath = '../../';
$error = '';

$pdo = Conexion::getInstancia();
$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

$stmt = $pdo->prepare('SELECT idUsuario AS id, nome AS nombre, email, tipoDeUsuario AS rol FROM usuarios WHERE idUsuario = :id');
$stmt->execute([':id' => $id]);
$usuarioEditado = $stmt->fetch();

if ($usuarioEditado === false) {
    http_response_code(404);
    die('Usuário não encontrado.');
}

//Essa linha vai fazer com que converta o tipo do banco (como funcionário, por exemplo) pra código php
$usuarioEditado['rol'] = Usuario::rolDesdeBd($usuarioEditado['rol']);

$rolesValidos = ['huesped', 'funcionario', 'dueno'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $rol = $_POST['rol'] ?? '';

    if ($nombre === '') {
        $error = 'O nome é obrigatório.';
    } elseif (!in_array($rol, $rolesValidos, true)) {
        $error = 'Rol inválido.';
    } elseif ($id === $usuarioActual->getId() && $rol !== 'dueno') {
        // Isso aqui evita do dono tirar o "rol" dele mesmo pelo erro e bloqueie o acesso
        $error = 'Não pode retirar-se como "dono".';
    } else {
        try {
            //Aqui ele traduz o ''rol'' escolhido pelo formato que o MySQL aceita no 'enum'
            $tipoDeUsuarioBd = Usuario::rolParaBd($rol);

            $stmt = $pdo->prepare('UPDATE usuarios SET nome = :nombre, tipoDeUsuario = :rol WHERE idUsuario = :id');
            $stmt->execute([':nombre' => $nombre, ':rol' => $tipoDeUsuarioBd, ':id' => $id]);
            header('Location: listar.php');
            exit;
        } catch (PDOException $e) {
            error_log('Erro ao editar o usuário: ' . $e->getMessage());
            $error = 'Falha em guardar alterações, tente novamente.';
        }
    }

    $usuarioEditado['nombre'] = $nombre;
    $usuarioEditado['rol'] = $rol;
}

require __DIR__ . '/../../includes/header.php';
?>

<section class="seccion seccion--angosta">
    <h1>Editar usuário</h1>

    <?php if ($error !== ''): ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="formulario">
        <input type="hidden" name="id" value="<?= (int) $usuarioEditado['id'] ?>">

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($usuarioEditado['nombre']) ?>">

        <label>Email</label>
        <input type="email" value="<?= htmlspecialchars($usuarioEditado['email']) ?>" disabled>
        <small class="texto-ayuda">El email no se puede modificar.</small>

        <label for="rol">Rol</label>
        <select id="rol" name="rol">
            <?php foreach (['huesped' => 'Huésped', 'funcionario' => 'Funcionario', 'dueno' => 'Dueño'] as $valor => $texto): ?>
                <option value="<?= $valor ?>" <?= $usuarioEditado['rol'] === $valor ? 'selected' : '' ?>><?= $texto ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn">Guardar cambios</button>
    </form>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
