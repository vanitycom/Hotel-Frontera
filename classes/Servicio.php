<?php
require_once __DIR__ . '/Conexion.php';

class Servicio{
    private ?int $id;
    private string $nombre;
    private ?string $descripcion;
    private ?int $categoriaId;
    private string $estado;
    private ?string $motivo;
    private ?int $actualizadoPor;

    public function __construct(?int $id, string $nombre, ?string $descripcion, ?int $categoriaId, string $estado, ?string $motivo, ?int $actualizadoPor){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->categoriaId = $categoriaId;
        $this->estado = $estado;
        $this->motivo = $motivo;
        $this->actualizadoPor = $actualizadoPor;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getNombre(): string{
        return $this->nombre;
    }

    public function getdescripcion(): ?string{
        return $this->descripcion;
    }

    public function getcategoriaId(): ?int{
        return $this->categoriaId;
    }

    public function getestado(): string {
        return $this->estado;
    }

    public function getmotivo(): ?string {
        return $this->motivo;
    }

    public function getactualizadoPor(): ?int{
        return $this->actualizadoPor;
    }

    /* La columna statusDoServico del banco guarda los valores en portugués
       ('disponível', 'manutenção', 'fechado'). Estos dos métodos traducen entre
       eso y el estado interno en español que usa el resto del código (activo,
       mantenimiento, cerrado), que es el que usan las clases CSS de style.css. */
    public static function estadoDesdeBd(string $statusDoServico): string
    {
        return match ($statusDoServico) {
            'manutenção' => 'mantenimiento',
            'fechado'    => 'cerrado',
            default      => 'activo',
        };
    }

    public static function estadoParaBd(string $estado): string
    {
        return match ($estado) {
            'mantenimiento' => 'manutenção',
            'cerrado'       => 'fechado',
            default         => 'disponível',
        };
    }

    public function crear(): bool
    {
        try {
            $pdo = Conexion::getInstancia();
            $sql = 'INSERT INTO servicos (nome, descricao, idCategoria, statusDoServico, motivo, atualizadoPor)
                    VALUES (:nombre, :descripcion, :categoria_id, :estado, :motivo, :actualizado_por)';
            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute([
                ':nombre'         => $this->nombre,
                ':descripcion'    => $this->descripcion,
                ':categoria_id'   => $this->categoriaId,
                ':estado'         => self::estadoParaBd($this->estado),
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
            $sql = 'SELECT s.idServico AS id, s.nome AS nombre, s.descricao AS descripcion,
                           s.idCategoria AS categoria_id, s.statusDoServico AS estado, s.motivo,
                           s.atualizadoPor AS actualizado_por, s.atualizadoEm AS actualizado_em,
                           c.nome AS categoria_nombre, u.nome AS actualizado_por_nombre
                    FROM servicos s
                    LEFT JOIN catservicos c ON c.idCat = s.idCategoria
                    LEFT JOIN usuarios u ON u.idUsuario = s.atualizadoPor
                    ORDER BY s.nome ASC';
            $stmt = $pdo->query($sql);
            $filas = $stmt->fetchAll();
            foreach ($filas as &$fila) {
                $fila['estado'] = self::estadoDesdeBd($fila['estado']);
            }
            return $filas;
        } catch (PDOException $e) {
            error_log('Error al listar servicios: ' . $e->getMessage());
            return [];
        }
    }


    public static function buscarPorId(int $id): ?array
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('SELECT idServico AS id, nome AS nombre, descricao AS descripcion,
                                           idCategoria AS categoria_id, statusDoServico AS estado, motivo,
                                           atualizadoPor AS actualizado_por, atualizadoEm AS actualizado_em
                                    FROM servicos WHERE idServico = :id');
            $stmt->execute([':id' => $id]);
            $fila = $stmt->fetch();
            if ($fila) {
                $fila['estado'] = self::estadoDesdeBd($fila['estado']);
            }
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
            return $pdo->query('SELECT idCat AS id, nome AS nombre FROM catservicos ORDER BY nome ASC')->fetchAll();
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
            $sql = 'UPDATE servicos
                    SET nome = :nombre, descricao = :descripcion, idCategoria = :categoria_id,
                        statusDoServico = :estado, motivo = :motivo, atualizadoPor = :actualizado_por
                    WHERE idServico = :id';
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nombre'          => $this->nombre,
                ':descripcion'     => $this->descripcion,
                ':categoria_id'    => $this->categoriaId,
                ':estado'          => self::estadoParaBd($this->estado),
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
            $stmt = $pdo->prepare('DELETE FROM servicos WHERE idServico = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Error al eliminar servicio: ' . $e->getMessage());
            return false;
        }
    }
}
