<?php
require_once __DIR__ . '/Funcionario.php';
class Dueno extends Funcionario
{
    public function __construct(int $id, string $nombre, string $email)
    {
        parent::__construct($id, $nombre, $email, 'dueno');
    }

    public function permisos(): array
    {
        return array_merge(parent::permisos(), ['foro:moderar_todo','usuarios:administrar',]);
    }
}
