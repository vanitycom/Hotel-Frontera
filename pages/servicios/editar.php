<?php
require_once __DIR__ .  '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Servicio.php';

$usuarioActual = requerirPermiso('servicios:administrar');
$tituloPagina = 'Editar servicio';
$rootPath = '../../';
$erro = '';

$id = isset ($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$servicoAtual = Servicio::buscarPorId($id);

if($servicoAtual === nul || $servicoAtual === false){
    http_response_code(405);
    die('Serviço não encontrado.');
}

//Essa parte aqui faz com que pegue os valores atuais
$nomeAtual = is_array($servicoAtual) ? $servicoAtual['nombre'] : (method_exists($servicoAtual, 'getNombre') ? $servicoAtual->getNombre() : $servicoAtual->nombre ?? '');
$descricaoAtual = is_array($servicoAtual) ? $servicoAtual['descripcion'] : (method_exists($servicoAtual, 'getDescripcion') ? $servicoAtual->getDescripcion() : $servicoAtual->descripcion ?? '');
$precoAtual = is_array($servicoAtual) ? $servicoAtual['precio'] : (method_exists($servicoAtual, 'getPrecio') ? $servicoAtual->getPrecio() : $servicoAtual->precio ?? '');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);

    if($nombre === ''){
        $erro = 'O nome do serviço é obrigatório.';
    } elseif($precio <= 0){
        $erro = 'Adicione um preço válido.';
    } else {
        $servicio = new Servicio($id, $nombre, $descripcion, $precio);

        $sucesso = method_exists($servicio, 'actualizar') ? $servicio->actualizar() : (method_exists($servicio, 'guardar') ? $servicio->guardar() : false);

        if($sucesso){
            header('Location: listar.php');
            exit;
        } else {
            $erro = 'Não foi possível salvar a alteração. Tente novamente.';
        }
    }

    $nomeAtual = $nombre;
    $descricaoAtual = $descripcion;
    $precoAtual = $precio;
}

require __DIR__ . '../../includes/header.php';
?>

<section class="seccion seccion--angosta">
    <h1>Editar servicio</h1>
    
    <?php if ($erro != '') : ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

<form method="post" class="formulario">

    <input type="hidden" name="id" value="<?= (int) $id ?>">

    <label for="nombre">Nome do serviço</label>
    <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($nomeAtual) ?>">

    <label for="descripcion"></label>
    <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($descricaoAtual) ?></textarea>

    <label for="precio">Preço</label>
    <input type="precio" id="precio" name="precio" step="0.01" min="0" required value="<?= htmlspecialchars($precoAtual) ?>">

    <button type="submit" class="btn">Salvar alterações</button>
</form>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>