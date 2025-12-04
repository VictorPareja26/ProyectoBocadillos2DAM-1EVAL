<?php
header("Content-Type: application/json");
require_once '../models/Pedido.php';

$respuesta = ["exito" => false, "mensaje" => "Acción no válida"];

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;

if (!$accion) {
    echo json_encode($respuesta);
    exit;
}

switch ($accion) {

    case 'listar':
        $pedidos = Pedido::getPedido();
        $respuesta = [
            "exito" => true,
            "datos" => $pedidos
        ];
        break;

    case 'marcarEntregado':

        if (!isset($_POST['id'])) {
            $respuesta["mensaje"] = "No se recibió el ID del pedido";
            break;
        }

        $ok = Pedido::marcarEntregado($_POST['id']);

        if ($ok) {
            $respuesta = [
                "exito" => true,
                "mensaje" => "Pedido marcado como entregado"
            ];
        }
        break;
}

echo json_encode($respuesta);
?>
