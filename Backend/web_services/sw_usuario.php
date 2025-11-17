<?php
require_once "../models/Usuario.php";
header('Content-Type: application/json; charset=utf-8');

// Leer la entrada JSON y obtener la acción
$input = json_decode(file_get_contents('php://input'), true);
$accion = $input['accion'] ?? null;

$data = [];
$count = 0;
$pages = 0;
$pagina = 1;
$success = false;
$msg = '';

try {
    switch ($accion) {

        case 'login':
            $usuario = new Usuario();
            $usuario->login();
            exit;



        case 'get':
            $pagina = isset($input['pagina']) ? (int)$input['pagina'] : 1;
            $nombre = isset($input['nombreUsuario']) ? $input['nombreUsuario'] : '';
            $usuariosPorPagina = 5;

            $inicio = ($pagina - 1) * $usuariosPorPagina;

            try {
                $pdo = Conexion::getInstancia()->getConexion();

                // Contar total de usuarios que coinciden
                $stmtCount = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios WHERE nombreUsuario LIKE ?");
                $stmtCount->execute(["%$nombre%"]);
                $count = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

                // Calcular total de páginas
                $pages = ceil($count / $usuariosPorPagina);

                // Obtener usuarios de la página actual
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombreUsuario LIKE ? LIMIT ?, ?");
                $stmt->bindValue(1, "%$nombre%", PDO::PARAM_STR);
                $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                $stmt->bindValue(3, $usuariosPorPagina, PDO::PARAM_INT);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $success = true;
            } catch (Exception $e) {
                $success = false;
                $msg = $e->getMessage();
            }
            break;


        case 'insert':
            $usuario = new Usuario($input["id"], $input["nombreUsuario"],
            $input["contrasenya"],$input["rol"], $input["correo"], $input["fecha"]);
            $row = $usuario->insert();

            if ($row) {
                $success = true;
                $msg = "Usuario insertado correctamente";
            } else {
                $success = false;
                $msg = "No se pudo insertar el usuario";
            }
            break;

        case 'delete':
            $id = $input['id'] ?? null;

            if ($id) {
                $usuario = new Usuario($id);
                $row = $usuario->delete();

                if ($row) {
                    $success = true;
                    $msg = "Usuario eliminado correctamente";
                } else {
                    $success = false;
                    $msg = "No se pudo eliminar el usuario";
                }
            } else {
                $success = false;
                $msg = "No se recibió el ID";
            }
            break;

        default:
            $msg = "Acción no reconocida";
            $success = false;
            break;
    }
} catch (Exception $e) {
    $msg = $e->getMessage();
    $success = false;
}

$salida = [
    "success" => $success,
    "msg" => $msg,
    "data" => $data,
    "count" => $count,
    "pages" => $pages,
    "current_page" => $pagina
];

echo json_encode($salida);
