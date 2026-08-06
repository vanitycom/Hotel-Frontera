<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/classes/Conexion.php';

$tituloPagina = 'Iniciar sesión';
$rootPath = '';
$error = '';

if (usuarioLogueado() !== null) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Completá email y contraseña.';
    } else {
        try {
            $pdo = Conexao::getInstancia();
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email');
            $stmt->execute([':email' => $email]);
            $fila = $stmt->fetch();

            if ($fila && password_verify($password, $fila['password_hash'])) {
                $_SESSION['usuario'] = [
                    'id'     => $fila['id'],
                    'nombre' => $fila['nombre'],
                    'email'  => $fila['email'],
                    'rol'    => $fila['rol'],
                ];
                header('Location: index.php');
                exit;
            }

            $error = 'Email o contraseña incorrectos.';
        } catch (PDOException $e) {
            error_log('Error en login: ' . $e->getMessage());
            $error = 'Ocurrió un error al iniciar sesión. Intentá de nuevo.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="seccion seccion--angosta">
    <h1>Iniciar sesión</h1>

    <?php if ($error !== ''): ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="formulario">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn">Ingresar</button>
    </form>

    <p>¿No tenés cuenta? <a href="register.php">Registrate como huésped</a></p>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>