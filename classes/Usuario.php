<?php
abstract class Usuario{
    protected int $id;
    protected string $nombre;
    protected string $email;
    protected string $rol;

    public function __construct(int $id, string $nombre, string $email, string $rol){
        $this ->id = $id;
        $this ->nombre = $nombre;
        $this ->email = $email;
        $this ->rol = $rol;
    }

    public function getId(){
        return $this->id;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getrol(){
        return $this->rol;
    }

    abstract public function permisos(): array;

    public function tienePermiso(string $permiso): bool
    {
        return in_array($permiso, $this->permisos(), true);
    }

    public static function crearDesdeFila(array $fila): Usuario
    {
        return match ($fila['rol']) {
            'dueno'       => new Dueno((int) $fila['id'], $fila['nombre'], $fila['email']),
            'funcionario' => new Funcionario((int) $fila['id'], $fila['nombre'], $fila['email']),
            default       => new Huesped((int) $fila['id'], $fila['nombre'], $fila['email']),
        };
    }
}

?>