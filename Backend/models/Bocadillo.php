<?php

require_once 'Conexion.php';
header('Content-Type: application/json');

class Bocadillo
{
    private $id;
    private $nombre;
    private $tipo;  
    private $dia;       
    private $precio;
    private $descripcion;
    private $imagen;

    public function __construct($id = null, $nombre = null, $tipo = null, $dia = null, $precio = null, $descripcion = null, $imagen = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->dia = $dia;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
    }

    /** Obtener todos los bocadillos del día */
    public static function getAll()
{
    $pdo = Conexion::getInstancia()->getConexion();

    // Comprobar si es sábado o domingo
    $diaSemana = date('w');

    if ($diaSemana == 0 || $diaSemana == 6) {
        return []; 
    }

    // Obtener el nombre del día en inglés (Monday, Tuesday, etc.)
    $nombreDia = date('l');
    
    $sql = "SELECT * FROM bocadillos WHERE dia = :dia";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':dia' => $nombreDia]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    /** Comprobar si el usuario ya ha pedido hoy */
    public static function yaPedidoHoy()
    {
        session_start();

        if (!isset($_SESSION['usuario_id']))
            return false;

        $pdo = Conexion::getInstancia()->getConexion();

        $sql = "SELECT COUNT(*) as total FROM pedidos WHERE usuario_id = :uid AND DATE(fecha) = CURDATE()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $_SESSION['usuario_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'] > 0;
    }

    /** Registrar un pedido */
    public static function pedir($id, $tipo)
    {
        session_start();

        if (!isset($_SESSION['usuario_id']))
            return false;

        $pdo = Conexion::getInstancia()->getConexion();
        
        $sql = "INSERT INTO pedidos (usuario_id, bocadillo_id, tipo, fecha) VALUES (:uid, :bid, :tipo, NOW())";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':uid' => $_SESSION['usuario_id'],
            ':bid' => $id,
            ':tipo' => $tipo
        ]);
    }

    /** Cancelar pedido del día */
    public static function cancelarPedido()
    {
        session_start();
        if (!isset($_SESSION['usuario_id']))
            return false;

        $pdo = Conexion::getInstancia()->getConexion();
        $sql = "DELETE FROM pedidos WHERE usuario_id = :uid AND DATE(fecha) = CURDATE()";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':uid' => $_SESSION['usuario_id']]);
    }

    /** Insertar un bocadillo nuevo en la carta */
    public function insert()
    {
        $pdo = Conexion::getInstancia()->getConexion();
        $params = [
            ':id' => $this->id,
            ':nombre' => $this->nombre,
            ':tipo' => $this->tipo,
            ':dia' => $this->dia,
            ':precio' => $this->precio,
            ':descripcion' => $this->descripcion,
            ':imagen' => $this->imagen
        ];

        $query = "INSERT INTO bocadillos (id, nombre, tipo, dia, precio, descripcion, imagen)
                  VALUES (:id, :nombre, :tipo, :dia, :precio, :descripcion, :imagen)";
        $stmt = $pdo->prepare($query);
        return $stmt->execute($params);
    }

    public function update()
    {
        $pdo = Conexion::getInstancia()->getConexion();
        $query = "UPDATE bocadillos 
                  SET nombre = :nombre, tipo = :tipo, dia = :dia, precio = :precio, descripcion = :descripcion, imagen = :imagen
                  WHERE id = :id";
        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            ':id' => $this->id,
            ':nombre' => $this->nombre,
            ':tipo' => $this->tipo,
            ':dia' => $this->dia,
            ':precio' => $this->precio,
            ':descripcion' => $this->descripcion,
            ':imagen' => $this->imagen
        ]);
    }

    public function delete()
    {
        $pdo = Conexion::getInstancia()->getConexion();
        $stmt = $pdo->prepare("DELETE FROM bocadillos WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
}

?>