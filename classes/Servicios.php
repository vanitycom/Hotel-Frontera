<?php
require_once __DIR__ . '/Conexion.php';

class Servicios{
     private int $id;
    private string $nombre;
    private ?string $descripcion;
    private ?int $categoriaId;
    private string $estado; 

    public function __construc(?int $id, string $nombre, ?string $descripcion, ?int $categoriaId, string $estado, ?string $motivo, ?int $actualizadoPor){
        $this->id             = $id;
        $this->nombre         = $nombre;
        $this->descripcion    = $descripcion;
        $this->categoriaId    = $categoriaId;
        $this->estado         = $estado;
        $this->motivo         = $motivo;
        $this->actualizadoPor = $actualizadoPor;
    }

    public function getId(): ?int{
        return $this->$id;
    }

        public function getNombre(): string{
        return $this->$nombre;
    }

        public function getdescripcion(): ?string{
        return $this->$descripcion;
    }

        public function getcategoriaId(): ?int{
        return $this->$categoriaId;
    }

        public function getestado(): string {
        return $this->$estado;
    }

        public function getmotivo(): ?string {
        return $this->$motivo;
    }

        public function getactualizadoPor(): ?int{
        return $this->$actualizadoPor;
    }



        public function crear(): bool
    {
        try {
            $pdo = Conexion::getInstancia();
            $sql = 'INSERT INTO servicios (nombre, descripcion, categoria_id, estado, motivo, actualizado_por)
                    VALUES (:nombre, :descripcion, :categoria_id, :estado, :motivo, :actualizado_por)';
            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute([
                ':nombre'         => $this->nombre,
                ':descripcion'    => $this->descripcion,
                ':categoria_id'   => $this->categoriaId,
                ':estado'         => $this->estado,
                ':motivo'         => $this->motivo,
                ':actualizado_por'=> $this->actualizadoPor,
            ]);
            if ($ok) {
                $this->id = (int) $pdo->lastInsertId();
            }
            return $ok;
        } catch (PDOException $e) {
            error_log('Error al crear servicio: ' . $e->getMessage());
            return false;
        }
    }


    
      public static function listarTodos(): array
    {
        try {
            $pdo = Conexion::getInstancia();
            $sql = 'SELECT s.*, c.nombre AS categoria_nombre, u.nombre AS actualizado_por_nombre
                    FROM servicios s
                    LEFT JOIN categorias_servicio c ON c.id = s.categoria_id
                    LEFT JOIN usuarios u ON u.id = s.actualizado_por
                    ORDER BY s.nombre ASC';
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error al listar servicios: ' . $e->getMessage());
            return [];
        }
    }


    public static function buscarPorId(int $id): ?array
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('SELECT * FROM servicios WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $fila = $stmt->fetch();
            return $fila ?: null;
        } catch (PDOException $e) {
            error_log('Error al buscar servicio: ' . $e->getMessage());
            return null;
        }
    }


    public static function listarCategorias(): array
    {
        try {
            $pdo = Conexion::getInstancia();
            return $pdo->query('SELECT * FROM categorias_servicio ORDER BY nombre ASC')->fetchAll();
        } catch (PDOException $e) {
            error_log('Error al listar categorías: ' . $e->getMessage());
            return [];
        }
    }


    public function actualizar(): bool
    {
        if ($this->id === null) {
            return false;
        }
        try {
            $pdo = Conexion::getInstancia();
            $sql = 'UPDATE servicios
                    SET nombre = :nombre, descripcion = :descripcion, categoria_id = :categoria_id,
                        estado = :estado, motivo = :motivo, actualizado_por = :actualizado_por
                    WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nombre'          => $this->nombre,
                ':descripcion'     => $this->descripcion,
                ':categoria_id'    => $this->categoriaId,
                ':estado'          => $this->estado,
                ':motivo'          => $this->motivo,
                ':actualizado_por' => $this->actualizadoPor,
                ':id'              => $this->id,
            ]);
        } catch (PDOException $e) {
            error_log('Error al actualizar servicio: ' . $e->getMessage());
            return false;
        }
    }


    public static function eliminar(int $id): bool
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('DELETE FROM servicios WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Error al eliminar servicio: ' . $e->getMessage());
            return false;
        }
    }
}