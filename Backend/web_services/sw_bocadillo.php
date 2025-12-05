<?php
require_once "../models/Bocadillo.php";
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$accion = $input['accion'] ?? null;

$data = [];
$success = false;
$msg = '';
$yaPedidoHoy = false;

try {
    switch ($accion) {

        case 'get':
            $bocadillos = Bocadillo::getAll();
            $data = $bocadillos;

            $yaPedidoHoy = Bocadillo::yaPedidoHoy();
            $success = true;

            if (empty($bocadillos)) {
                $msg = "Hoy es sabado o domingo, no hay bocadillos disponibles";
            }
            break;


        case 'insert':

            $id = $input['bocadillo_id'] ?? null;
            $tipo = $input['tipo'] ?? null;

            if ($id && $tipo) {

                $pedido = Bocadillo::pedir($id, $tipo);
                
                if ($pedido) {
                    $success = true;
                    $msg = "Pedido realizado correctamente";
                    $yaPedidoHoy = true;
                } else {
                    $success = false;
                    $msg = "No se pudo realizar el pedido";
                }
            } else {
                $success = false;
                $msg = "Datos incompletos para realizar el pedido";
            }
            break;

        case 'cancelar':
            $cancelado = Bocadillo::cancelarPedido();
            if ($cancelado) {
                $success = true;
                $msg = "Pedido cancelado correctamente";
                $yaPedidoHoy = false;
            } else {
                $success = false;
                $msg = "No se pudo cancelar el pedido";
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
    "yaPedidoHoy" => $yaPedidoHoy
];

echo json_encode($salida);
?>