<?php 
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../classes/Conexion.php';

$usuarioActual = requerirPermiso('usuarios:administrar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    die('Método não permitido.');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0 && $id !== $usuarioActual->getId()){
    try{
        $pdo = Conexion::getInstancia();
        $stmt = $pdo->prepare('DELETE FROM usuarios WHERE idUsuario = :id');
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e){
        error_log('Erro ao excluir um usuário: ' . $e->getMessage());
    }
}

header('Location: listar.php');
exit;
?>