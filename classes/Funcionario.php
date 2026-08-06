<?php
require_once __DIR__ . '/Usuario.php';
/* Permiso de editar borrar y crear en el foro y lo mismo en servicios */
class Funcionario extends Usuario
{
    public function __construct(int $id, string $nombre, string $email, string $rol = 'funcionario')
    {
        parent::__construct($id, $nombre, $email, $rol);
    }

    public function permisos(): array
    {
        return ['servicios:crear', 'servicios:editar', 'servicios:borrar', 'foro:crear', 'foro:borrar_propio', 'foro:editar_propio'];
    }
}