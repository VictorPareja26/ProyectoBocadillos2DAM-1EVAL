<?php
class Conexion {
    private static ?Conexion $instancia = null;
    private PDO $conexion;

    // Configuración de la base de datos
    private string $host = 'localhost';
    private string $dbName = 'bdbocadillos';
    private string $user = 'root';
    private string $pass = '';
    private int $port = 3306; // Cambia esto si usas otro puerto (ej. 3307)

    // Constructor privado (patrón Singleton)
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbName};charset=utf8";
            $this->conexion = new PDO($dsn, $this->user, $this->pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // En proyecto de clase se puede hacer un die, en producción no
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Devuelve siempre la misma instancia
    public static function getInstancia(): Conexion {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    // Devuelve el objeto PDO para hacer consultas
    public function getConexion(): PDO {
        return $this->conexion;
    }
}
?>
