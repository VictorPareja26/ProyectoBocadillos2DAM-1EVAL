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

    $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;
    $offset = 5; // 5 pedidos por página

    $pdo = Conexion::getInstancia()->getConexion();

    // Contar solo pedidos del día actual
    $countQuery = "SELECT COUNT(*) 
                   FROM pedidos 
                   WHERE DATE(fecha) = CURDATE()";
    $total = $pdo->query($countQuery)->fetchColumn();

    // Calcular total de páginas (mínimo 1)
    $totalPaginas = max(1, ceil($total / $offset));

    // Ajustar páginas límite
    if ($pagina > $totalPaginas) $pagina = $totalPaginas;
    if ($pagina < 1) $pagina = 1;

    $inicio = ($pagina - 1) * $offset;

    // Obtener pedidos del día actual con paginación
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
            WHERE DATE(p.fecha) = CURDATE()
            ORDER BY p.id DESC
            LIMIT :inicio, :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":inicio", $inicio, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $respuesta = [
        "exito" => true,
        "datos" => $pedidos,
        "total" => $total,
        "pagina" => $pagina,
        "totalPaginas" => $totalPaginas
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
