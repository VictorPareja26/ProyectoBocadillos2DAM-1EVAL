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
            $usuarioNombre = $input['usuario'] ?? null;
            $contrasenya = $input['contrasenya'] ?? null;

            $usuario = new Usuario(null, $usuarioNombre, $contrasenya);
            $usuario->login();
            exit;

        case 'get':
            $id = isset($input['id']) ? (int) $input['id'] : null;

            // Si viene un ID, devolver solo ese usuario
            if ($id) {
                $usuario = Usuario::get($id);

                if ($usuario) {
                    $success = true;
                    $data = $usuario;
                } else {
                    $success = false;
                    $msg = 'Usuario no encontrado';
                }
            }
            // Si no viene ID, listar usuarios con filtros y paginación
            else {
                $pagina = isset($input['pagina']) ? (int) $input['pagina'] : 1;
                $nombre = isset($input['nombreUsuario']) ? $input['nombreUsuario'] : '';

                try {
                    // Preparar filtros
                    $filters = [];
                    if (!empty($nombre)) {
                        $filters['nombreUsuario'] = $nombre;
                    }

                    // Contar total de usuarios con filtros
                    $count = Usuario::count($filters);

                    // Calcular total de páginas
                    $pages = Usuario::getPages($count, 5);

                    // Obtener usuarios con filtros y paginación
                    $data = Usuario::find($filters, $pagina, 5);

                    $success = true;
                } catch (Exception $e) {
                    $success = false;
                    $msg = $e->getMessage();
                }
            }
            break;

        case 'insert':

            $usuario = new Usuario(
                $input["id"],
                $input["nombreUsuario"],
                $input["contrasenya"],
                $input["rol"],
                $input["correo"],
                $input["fecha"]
            );


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

        case 'update':
            $id = $input['id'] ?? null;

            if ($id) {
                $usuario = new Usuario(
                    $id,
                    $input["nombreUsuario"],
                    $input["contrasenya"],
                    $input["rol"],
                    $input["correo"],
                    $input["fecha"] ?? null
                );
                $row = $usuario->update();

                if ($row) {
                    $success = true;
                    $msg = "Usuario actualizado correctamente";
                } else {
                    $success = false;
                    $msg = "No se pudo actualizar el usuario";
                }
            } else {
                $success = false;
                $msg = "No se recibió el ID del usuario";
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

?>