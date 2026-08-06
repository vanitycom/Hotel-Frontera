<?php
require_once __DIR__ . '/../classes/Usuario.php';
require_once __DIR__ . '/../classes/Huesped.php';
require_once __DIR__ . '/../classes/Funcionario.php';
require_once __DIR__ . '/../classes/Dueno.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioLogueado(): ?Usuario
{
    if (!isset($_SESSION['usuario'])) {
        return null;
    }
    
    return Usuario::crearDesdeFila($_SESSION['usuario']);
}

function requerirLogin(): Usuario
{
    $usuario = usuarioLogueado();
    if ($usuario === null) {
        header('Location: /login.php');
        exit;
    }
    return $usuario;
}

function requerirPermiso(string $permiso): Usuario
{
    $usuario = requerirLogin();
    if (!$usuario->tienePermiso($permiso)) {
        http_response_code(403);
        die('Acceso denegado: no tenés permiso para realizar esta acción.');
    }
    return $usuario;
}
