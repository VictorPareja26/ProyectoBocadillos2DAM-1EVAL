<?php

require_once 'Conexion.php';


class Pedido
{

    //Variable
    private $id;
    private $usuario_id;
    private $bocadillo_id;
    private $fecha;
    private $tipo;

    //Constructor
    public function __construct($id = null, $usuario_id = null, $bocadillo_id = null, $fecha = null, $tipo = null)
    {
        $this->id = $id;
        $this->usuario_id = $usuario_id;
        $this->tipo = $tipo;
        $this->bocadillo_id = $bocadillo_id;
        $this->fecha = $fecha;

    }
    //Terminar getPedido()
    public static function getPedido(){

        $pdo = Conexion::getInstancia()->getConexion();


    }
    public function insert(){

        $pdo = Conexion::getInstancia()->getConexion();

        // Preparar los parámetros primero
        $params = [
            ':id' => $this->id,
            ':nombre' => $this->usuario_id,
            ':tipo' => $this->tipo,
            ':bocadillo_id' => $this->bocadillo_id,

        ];

        $query = "INSERT INTO pedidos (id, usuario_id, tipo, bocadillo_id, fecha)
              VALUES (:id, :usuario_id, :tipo, :bocadillo_id, NOW())";

        $stmt = $pdo->prepare($query);
        return $stmt->execute($params);


    }

    public function delete()
    {
        $pdo = Conexion::getInstancia()->getConexion();


        // Luego borrar el alumno
        $stmt1 = $pdo->prepare("DELETE FROM pedidos WHERE id = :id");
        $row = $stmt1->execute([':id' => $this->id]);

        return true;
    }

    public function modifye(){
        
    }

    //  Pasar A Pedido
    public static function find($filters=[], $page = 1 ,$offset = self::OFFSET){}

    //  Pasar A Pedido
    public static function count($filters=[]){
    }

    public static function getPages($num_registros, $offset = self::OFFSET){
        if ($num_registros == 0){ 
            return 1;
        }
        return ceil($num_registros / $offset);
         
    }
}