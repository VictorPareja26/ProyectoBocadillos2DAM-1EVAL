<?php

require_once 'Conexion.php';
header('Content-Type: application/json');

class Pedido
{
    private $id;
    private $usuario_id;
    private $bocadillo_id;
    private $fecha;
    private $tipo;
    private $estado; // NUEVA COLUMNA

    public function __construct($id = null, $usuario_id = null, $bocadillo_id = null, $fecha = null, $tipo = null, $estado = "Pendiente")
    {
        $this->id = $id;
        $this->usuario_id = $usuario_id;
        $this->bocadillo_id = $bocadillo_id;
        $this->fecha = $fecha;
        $this->tipo = $tipo;
        $this->estado = $estado;
    }

    // --------------------------------------------------------------
    // LISTAR TODOS LOS PEDIDOS — PARA COCINA
    // --------------------------------------------------------------
    public static function getPedido()
    {
        $pdo = Conexion::getInstancia()->getConexion();

        $sql = "SELECT 
                    p.id,
                    u.nombreUsuario AS alumno,
                    b.nombre AS bocadillo,
                    p.tipo,
                    p.fecha,
                    p.estado
                FROM pedidos p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                INNER JOIN bocadillos b ON p.bocadillo_id = b.id
                ORDER BY p.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --------------------------------------------------------------
    // INSERTAR PEDIDO (lo usa el alumno)
    // --------------------------------------------------------------
    public function insert()
    {
        $pdo = Conexion::getInstancia()->getConexion();

        $sql = "INSERT INTO pedidos (usuario_id, bocadillo_id, tipo, fecha, estado)
                VALUES (:usuario_id, :bocadillo_id, :tipo, NOW(), 'Pendiente')";

        $stmt = $pdo->prepare($sql);

        $params = [
            ':usuario_id' => $this->usuario_id,
            ':bocadillo_id' => $this->bocadillo_id,
            ':tipo' => $this->tipo
        ];

        return $stmt->execute($params);
    }

    // --------------------------------------------------------------
    // MARCAR ENTREGADO (para cocina)
    // --------------------------------------------------------------
    public static function marcarEntregado($idPedido)
    {
        $pdo = Conexion::getInstancia()->getConexion();

        $sql = "UPDATE pedidos SET estado = 'Completa' WHERE id = ?";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$idPedido]);
    }

    // --------------------------------------------------------------
    // BORRAR PEDIDO
    // --------------------------------------------------------------
    public function delete()
    {
        $pdo = Conexion::getInstancia()->getConexion();

        $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id = :id");

        return $stmt->execute([':id' => $this->id]);
    }

    // --------------------------------------------------------------
    // VACÍOS (no se usan en cocina)
    // --------------------------------------------------------------
    public function modifye(){}

    public static function find($filters = [], $page = 1, $offset = self::OFFSET){}

    public static function count($filters = []){}

    public static function getPages($num_registros, $offset = self::OFFSET)
    {
        if ($num_registros == 0) {
            return 1;
        }
        return ceil($num_registros / $offset);
    }
}
