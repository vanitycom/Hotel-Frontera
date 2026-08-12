<?php
require_once __DIR__ .  '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Comentario.php';

$usuarioActual = requerirPermiso('servicios:administrar');
$tituloPagina = 'Cadastrar serviço';
$rootPath = '../../';
$erro = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);

    if($nombre === ''){
        $erro = 'O nome do serviço é obrigatório.';
    } elseif($precio <= 0){
        $error = 'Informe um preço válido';
    } else {
        $servicio = new Servicio(null, $nombre, $descripcion, $precio);
        if($servicio->crear()){
            header('Location: listar.php');
            exit;
        } else {
            $error = 'Erro ao cadastrar serviço. Tente novamente.';
        }
    }
}

require __DIR__ . '/../../includes/header.php';
?>

<section class="seccion seccion--angosta">
    <h1>Cadastrar serviço</h1>
<?php if ($erro != '') : ?>
    <p class="alerta alerta--error"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form method="post" class="formulario">
    <label for="nombre">Nome do serviço</label>
    <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">

    <label for="descripcion"></label>
    <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>

    <label for="precio">Preço</label>
    <input type="precio" id="precio" name="precio" step="0.01" min="0" required value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">

    <button type="submit" class="btn">Cadastrar</button>
</form>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>