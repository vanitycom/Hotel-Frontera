<?php
require_once __DIR__ . '/Conexion.php';

class Comentario
{
    private ?int $id;
    private int $usuarioId;
    private string $contenido;
    private bool $editado;
    private bool $fijado;

    public function __construct(?int $id, int $usuarioId, string $contenido, bool $editado = false, bool $fijado = false)
    {
        $this->id        = $id;
        $this->usuarioId = $usuarioId;
        $this->contenido = $contenido;
        $this->editado   = $editado;
        $this->fijado    = $fijado;
    }

    public function getId(): ?int { return $this->id; }
    public function getUsuarioId(): int { return $this->usuarioId; }
    public function getContenido(): string { return $this->contenido; }

    public function crear(): bool
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('INSERT INTO comentarios (usuario_id, contenido) VALUES (:usuario_id, :contenido)');
            $ok = $stmt->execute([
                ':usuario_id' => $this->usuarioId,
                ':contenido'  => $this->contenido,
            ]);
            if ($ok) {
                $this->id = (int) $pdo->lastInsertId();
            }
            return $ok;
        } catch (PDOException $e) {
            error_log('Error al crear comentario: ' . $e->getMessage());
            return false;
        }
    }

    public static function listarTodos(): array
    {
        try {
            $pdo = Conexion::getInstancia();
            $sql = 'SELECT c.*, u.nombre AS autor_nombre, u.rol AS autor_rol
                    FROM comentarios c
                    JOIN usuarios u ON u.id = c.usuario_id
                    ORDER BY c.fijado DESC, c.fecha DESC';
            return $pdo->query($sql)->fetchAll();
        } catch (PDOException $e) {
            error_log('Error al listar comentarios: ' . $e->getMessage());
            return [];
        }
    }

    public static function buscarPorId(int $id): ?array
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('SELECT * FROM comentarios WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $fila = $stmt->fetch();
            return $fila ?: null;
        } catch (PDOException $e) {
            error_log('Error al buscar comentario: ' . $e->getMessage());
            return null;
        }
    }

    public function actualizar(): bool
    {
        if ($this->id === null) {
            return false;
        }
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('UPDATE comentarios SET contenido = :contenido, editado = 1 WHERE id = :id');
            return $stmt->execute([
                ':contenido' => $this->contenido,
                ':id'        => $this->id,
            ]);
        } catch (PDOException $e) {
            error_log('Error al actualizar comentario: ' . $e->getMessage());
            return false;
        }
    }

    public static function alternarFijado(int $id): bool
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('UPDATE comentarios SET fijado = NOT fijado WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Error al fijar comentario: ' . $e->getMessage());
            return false;
        }
    }

    public static function eliminar(int $id): bool
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('DELETE FROM comentarios WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Error al eliminar comentario: ' . $e->getMessage());
            return false;
        }
    }
}

?>