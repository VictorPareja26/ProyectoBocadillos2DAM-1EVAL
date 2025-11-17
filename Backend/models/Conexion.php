<?php
class Conexion {
    private static ?Conexion $instancia = null;
    private PDO $conexion;

    // Configuración de la base de datos
    private string $host = 'localhost';
    private string $dbName = 'bdbocadillos';
    private string $user = 'root';
    private string $pass = '';
    private array $ports = ['3306', '3307'];

    // Constructor privado
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8;port={$this->port}";
            $this->conexion = new PDO($dsn, $this->user, $this->pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Método estático para obtener la instancia única
    public static function getInstancia(): Conexion {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    // Método para acceder a la conexión PDO
    public function getConexion(): PDO {
        return $this->conexion;
    }
}
?>