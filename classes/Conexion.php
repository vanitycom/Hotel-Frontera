<?php
class conexao{
    private static ?PDO $instancia = null;

    private const HOST   = 'localhost';
    private const DBNAME = 'hotel_crud';
    private const USER   = 'root';
    private const PASS   = '';

        private function __construct(){
    }

    public static function getInstacia(): PDO{
        if(self::$instancia === null) {
            try{
                 self::$instancia = new PDO($dsn, self::USER, self::PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('Error al conectar con la base de datos: ' . $e->getMessage());
            }
        }

    }
}
?>