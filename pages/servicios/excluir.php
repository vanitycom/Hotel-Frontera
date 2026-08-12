<?php
require_once __DIR__ .  '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Servicio.php';

$usuarioActual = requerirPermiso('servicios:administrar');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    die('Método não permitido.');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0){
    Servicio::eliminar($id);
}

header('Location: listar.php');
exit;
?>