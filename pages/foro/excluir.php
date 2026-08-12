<?php
require_once __DIR__ .  '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Comentario.php';

$usuarioActual = requerirLogin();

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    die('Método não permitido.');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0){
    $comentario = Comentario::buscarPorId($id);

    if($comentario !== null){
        $esPropio = (int) $comentario['usuario_id'] === $usuarioActual->getId();
        $puedeBorrar = ($esPropio && $usuarioActual->tienePermiso('foro:borrar_propio'))
        || $usuarioActual->tienePermiso('foro:moderar_todo');

        if($puedeBorrar){
            Comentario::eliminar($id);
        }
    }
}

header('Location: listar.php');
exit;
?>