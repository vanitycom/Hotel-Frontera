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

    public function getId(): int{
        return $this->id;
    }

    public function getNombre(): string{
        return $this->nombre;
    }

    public function getEmail(): string{
        return $this->email;
    }

    public function getRol(): string{
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

    /* La columna tipoDeUsuario del banco (banco.sql) guarda los valores en portugués
       y con tildes ('hóspede', 'funcionário', 'dono'). Estos dos métodos traducen entre
       eso y el rol interno en español que usa el resto del código. */
    public static function rolDesdeBd(string $tipoDeUsuario): string
    {
        return match ($tipoDeUsuario) {
            'dono'         => 'dueno',
            'funcionário'  => 'funcionario',
            default        => 'huesped',
        };
    }

    public static function rolParaBd(string $rol): string
    {
        return match ($rol) {
            'dueno'       => 'dono',
            'funcionario' => 'funcionário',
            default       => 'hóspede',
        };
    }
}

?>