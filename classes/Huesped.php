<?php
require_once __DIR__ . '/Usuario.php';
/* Permiso de crear editar y borrar comentarios propios */

class Huesped extends Usuario{
    public function __construct(int $id, string $nombre, string $email){
        parent:: __construct($id, $nombre ,$email,'huesped');
    }
    public function permisos(): array{
        return ['foro:crear', 'foro:editar_propio', 'foro:borrar_propio'];
    }
}




?>