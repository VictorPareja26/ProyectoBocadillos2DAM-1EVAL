<?php

require_once 'Conexion.php';
header('Content-Type: application/json');


class Usuario
{

    //Variable
    private $id;
    private $nombreUsuario;
    private $contrasenya;
    private $rol;
    private $correo;
    private $fecha;
    const OFFSET = 5;

    //Constructor
    public function __construct($id = null, $nombreUsuario = null, $contrasenya = null, $rol = null, $correo = null, $fecha = null)
    {
        $this->id = $id;
        $this->nombreUsuario = $nombreUsuario;
        $this->contrasenya = $contrasenya;
        $this->rol = $rol;
        $this->correo = $correo;
        $this->fecha = $fecha;
    }

    //Funciones
    public static function get($id) {
        $pdo = Conexion::getInstancia()->getConexion();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function login(){
        session_start();


        try {
            
            $pdo = Conexion::getInstancia()->getConexion();

            // Verificar usuario y contraseña
            $sql = "SELECT * FROM usuarios WHERE nombreUsuario = ? AND contrasenya = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$this->nombreUsuario, $this->contrasenya]);

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();

                // Guardar sesión
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombreUsuario'] = $user['nombreUsuario'];
                $_SESSION['rol'] = $user['rol'];

                // Redirigir según el rol
                if ($user['rol'] === 'Administrador') {
                    $pagina = 'Administrador.html';
                } else if ($user['rol'] === 'Cocina') {
                    $pagina = 'PerfilCocina.html';
                } else {
                    $pagina = 'MenuBocadillo.html';
                }

                echo json_encode(['exito' => true, 'pagina' => $pagina]);

            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'Usuario o contraseña incorrectos']);
            }

        } catch (Exception $e) {
            echo json_encode(['exito' => false, 'mensaje' => 'Error del servidor']);
        }


    }


    public function insert(){


        $pdo = Conexion::getInstancia()->getConexion();

        $params = [
            ':id' => $this->id,
            ':nombreUsuario' => $this->nombreUsuario,
            ':contrasenya' => $this->contrasenya,
            ':rol' => $this->rol,
            ':correo' => $this->correo,
            ':fecha' => $this->fecha ? $this->fecha : date('Y-m-d H:i:s')
        ];

        $query = "INSERT INTO usuarios (id, nombreUsuario, contrasenya, rol, correo, fecha)
              VALUES (:id, :nombreUsuario, :contrasenya, :rol, :correo, :fecha)";

        $stmt = $pdo->prepare($query);
        return $stmt->execute($params);
    }

    public function delete()
    {
        $pdo = Conexion::getInstancia()->getConexion();


        // Borrar el Usuario
        $stmt1 = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $row = $stmt1->execute([':id' => $this->id]);

        return true;
    }

    public function update(){
        $pdo = Conexion::getInstancia()->getConexion();

        $query = "UPDATE usuarios 
                  SET nombreUsuario = :nombreUsuario, 
                      contrasenya = :contrasenya, 
                      rol = :rol, 
                      correo = :correo, 
                      fecha = :fecha 
                  WHERE id = :id";
        
        $stmt = $pdo->prepare($query);

        $params = [
            ':id' => $this->id,
            ':nombreUsuario' => $this->nombreUsuario,
            ':contrasenya' => $this->contrasenya,
            ':correo' => $this->correo,
            ':rol' => $this->rol,
            ':fecha' => $this->fecha ? $this->fecha : date('Y-m-d H:i:s')
        ];

        $stmt->execute($params);

        return $stmt->rowCount();
    }

    public static function find($filters = [], $page = 1, $offset = self::OFFSET){
        $pdo = Conexion::getInstancia()->getConexion();
        $sql = "SELECT * FROM usuarios WHERE 1=1";
        $params = [];

        // Filtro por nombre de usuario
        if (!empty($filters['nombreUsuario'])) {
            $sql .= " AND nombreUsuario LIKE :nombreUsuario";
            $params[':nombreUsuario'] = "%" . $filters['nombreUsuario'] . "%";
        }

        // Filtro por correo
        if (!empty($filters['correo'])) {
            $sql .= " AND correo LIKE :correo";
            $params[':correo'] = "%" . $filters['correo'] . "%";
        }

        // Filtro por rol
        if (!empty($filters['rol'])) {
            $sql .= " AND rol = :rol";
            $params[':rol'] = $filters['rol'];
        }

        // Paginación
        $inicio = ($page - 1) * $offset;
        $sql .= " LIMIT $inicio, $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método count para contar registros con filtros
    public static function count($filters = []){
        $pdo = Conexion::getInstancia()->getConexion();
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE 1=1";
        $params = [];

        // Filtro por nombre de usuario
        if (!empty($filters['nombreUsuario'])) {
            $sql .= " AND nombreUsuario LIKE :nombreUsuario";
            $params[':nombreUsuario'] = "%" . $filters['nombreUsuario'] . "%";
        }

        // Filtro por correo
        if (!empty($filters['correo'])) {
            $sql .= " AND correo LIKE :correo";
            $params[':correo'] = "%" . $filters['correo'] . "%";
        }

        // Filtro por rol
        if (!empty($filters['rol'])) {
            $sql .= " AND rol = :rol";
            $params[':rol'] = $filters['rol'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public static function getPages($num_registros, $offset = self::OFFSET){
        if ($num_registros == 0){ 
            return 1;
        }
        return ceil($num_registros / $offset);
         
    }

}
?>