<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/classes/Conexion.php';

$tituloPagina = 'Registro';
$rootPath = '';
$error = '';

if (usuarioLogueado() !== null) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if ($nombre === '' || $email === '' || $password === '') {
        $error = 'Completá todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        try {
            $pdo = Conexao::getInstancia();

            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $error = 'Ya existe una cuenta con ese email.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    'INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (:nombre, :email, :hash, "huesped")'
                );
                $stmt->execute([':nombre' => $nombre, ':email' => $email, ':hash' => $hash]);

                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Error en registro: ' . $e->getMessage());
            $error = 'Ocurrió un error al registrarte. Intentá de nuevo.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="seccion seccion--angosta">
    <h1>Crear cuenta de huésped</h1>

    <?php if ($error !== ''): ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="formulario">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required minlength="6">

        <label for="password_confirm">Confirmar contraseña</label>
        <input type="password" id="password_confirm" name="password_confirm" required minlength="6">

        <button type="submit" class="btn">Registrarme</button>
    </form>

    <p>¿Ya tenés cuenta? <a href="login.php">Iniciar sesión</a></p>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>