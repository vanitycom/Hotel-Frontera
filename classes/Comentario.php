<?php
require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/Usuario.php';

class Comentario
{
    private ?int $id;
    private int $usuarioId;
    private string $contenido;

    public function __construct(?int $id, int $usuarioId, string $contenido)
    {
        $this->id        = $id;
        $this->usuarioId = $usuarioId;
        $this->contenido = $contenido;
    }

    public function getId(): ?int { return $this->id; }
    public function getUsuarioId(): int { return $this->usuarioId; }
    public function getContenido(): string { return $this->contenido; }

    public function crear(): bool
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('INSERT INTO comentarios (usuarioId, conteudo) VALUES (:usuario_id, :contenido)');
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
            $sql = 'SELECT c.idComentario AS id, c.usuarioId AS usuario_id, c.conteudo AS contenido,
                           c.datacomentario AS fecha, u.nome AS autor_nombre, u.tipoDeUsuario AS autor_rol
                    FROM comentarios c
                    JOIN usuarios u ON u.idUsuario = c.usuarioId
                    ORDER BY c.datacomentario DESC';
            $filas = $pdo->query($sql)->fetchAll();
            foreach ($filas as &$fila) {
                $fila['autor_rol'] = Usuario::rolDesdeBd($fila['autor_rol']);
            }
            return $filas;
        } catch (PDOException $e) {
            error_log('Error al listar comentarios: ' . $e->getMessage());
            return [];
        }
    }

    public static function buscarPorId(int $id): ?array
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('SELECT idComentario AS id, usuarioId AS usuario_id, conteudo AS contenido,
                                           datacomentario AS fecha
                                    FROM comentarios WHERE idComentario = :id');
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
            $stmt = $pdo->prepare('UPDATE comentarios SET conteudo = :contenido WHERE idComentario = :id');
            return $stmt->execute([
                ':contenido' => $this->contenido,
                ':id'        => $this->id,
            ]);
        } catch (PDOException $e) {
            error_log('Error al actualizar comentario: ' . $e->getMessage());
            return false;
        }
    }

    public static function eliminar(int $id): bool
    {
        try {
            $pdo = Conexion::getInstancia();
            $stmt = $pdo->prepare('DELETE FROM comentarios WHERE idComentario = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Error al eliminar comentario: ' . $e->getMessage());
            return false;
        }
    }
}
