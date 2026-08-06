<?php
require_once __DIR__ . '/Usuario.php';
/* Permiso de crear editar y borrar comentarios propios */

class Huesped extends Usuario{
    public function __construct(int $id, string $nome, string $email){
        parent:: __construct($id, $nombrev,$email,'huesped');
    }
    public function permisos(): array{
        return ['foro:crear', 'foro:editar_propio', 'foro:borrar_propio'];
    }
}




?>