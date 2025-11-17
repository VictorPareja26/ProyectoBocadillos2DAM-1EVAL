<?php

require_once 'Conexion.php';
header('Content-Type: application/json');


class Bocadillo
{

    //Variable
    private $id;
    private $nombre;
    private $tipo;
    private $dia;
    private $precio;
    private $descripcion;
    private $imagen;

    //Constructor
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

    public static function getBocadillo(){

        $pdo = Conexion::getInstancia()->getConexion();


    }

    public function insert(){

        
        $pdo = Conexion::getInstancia()->getConexion();

        // Preparar los parámetros primero
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
    public function modifye(){
        
    }
    public function delete(){

        $pdo = Conexion::getInstancia()->getConexion();

        // Borrar el bocadillo
        $stmt1 = $pdo->prepare("DELETE FROM bocadillos WHERE id = :id");
        $row = $stmt1->execute([':id' => $this->id]);

        return true;
    


        
    }

    //Pasar A Bocadillo
    public static function find($filters=[], $page = 1 ,$offset = self::OFFSET){

        $pdo = Conexion::getInstancia()->getConexion();
        $sql = "SELECT * FROM ALUMNO WHERE 1=1";
        $params = [];

        if (!empty($filters['nombre'])) {
            $sql .= " AND NOMBRE LIKE :nombre";
            $params[':nombre'] = "%" . $filters['nombre'] . "%";
        }
        if (!empty($filters['localidad'])) {
            $sql .= " AND LOCALIDAD LIKE :localidad";
            $params[':localidad'] = "%" . $filters['localidad'] . "%";
        }
        if (!empty($filters['dni'])) {
            $sql .= " AND DNI LIKE :dni";
            $params[':dni'] = "%" . $filters['dni'] . "%";
        }

        $inicio = ($page - 1) * $offset;
        $sql .= " LIMIT $inicio, $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);



    }

    //Pasar A Bocadillo
    public static function count($filters=[]){

        $pdo = Conexion::getInstancia()->getConexion();
        $sql = "SELECT COUNT(*) AS total FROM ALUMNO WHERE 1=1";
        $params = [];

        if (!empty($filters['nombre'])) {
            $sql .= " AND NOMBRE LIKE :nombre";
            $params[':nombre'] = "%" . $filters['nombre'] . "%";
        }
        if (!empty($filters['localidad'])) {
            $sql .= " AND LOCALIDAD LIKE :localidad";
            $params[':localidad'] = "%" . $filters['localidad'] . "%";
        }
        if (!empty($filters['dni'])) {
            $sql .= " AND DNI LIKE :dni";
            $params[':dni'] = "%" . $filters['dni'] . "%";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public static function getPages($num_registros, $offset = self::OFFSET){
        if ($num_registros == 0) return 1;
        return ceil($num_registros / $offset);
         
    }
}