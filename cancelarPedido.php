<?php
session_start();

try {
    //Conexion con la base de datos
    $host = 'localhost';
    $dbname = 'bdbocadillos';
    $user = 'root';
    $pass = '';
    $port = "3307";
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mensaje = "";

    // Verifica si la solicitud es POST y si se recibió un 'eliminar_id' (para eliminar un pedido).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $eliminar_id = $_POST['eliminar_id'];

    // Prepara la consulta SQL para eliminar el pedido con el ID recibido.
    $sqlDelete = "DELETE FROM pedidos WHERE id = :id";
    $stmtDelete = $pdo->prepare($sqlDelete);

    // Ejecuta la consulta pasando el ID como parámetro.
    $stmtDelete->execute([':id' => $eliminar_id]);

    $mensaje = "Pedido cancelado correctamente.";
}

    //La consulta para mostrar los pedidos con informacion del usuario y del bocadillo
    $stmt = $pdo->query("SELECT pedidos.id, usuarios.nombreUsuario AS usuario, bocadillos.nombre AS bocadillo, bocadillos.tipo
                                FROM pedidos
                                JOIN usuarios ON pedidos.usuario_id = usuarios.id
                                JOIN bocadillos ON pedidos.bocadillo_id = bocadillos.id
                                ORDER BY pedidos.id DESC
                            ");

    // Obtiene todos los resultados como un arreglo asociativo.
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $mensaje = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="cancelarPedido.css">
    <title>Cancelar Pedido</title>
</head>
<body>
    <div id="PseudoFondo">
        <h1>Lista de Pedidos</h1>

        <?php if ($mensaje): ?>
            <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <div class="FormularioInicio">
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Bocadillo</th>
                        <th>Tipo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['usuario']) ?></td>
                            <td><?= htmlspecialchars($p['bocadillo']) ?></td>
                            <td><?= htmlspecialchars($p['tipo']) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('¿Seguro que quieres cancelar este pedido?');">
                                    <input type="hidden" name="eliminar_id" value="<?= $p['id'] ?>" />
                                    <button id="BotonInicioSesion" type="submit">
                                        <img src="./Image/MenuAdministrador/usuario.png" alt="Cancelar" width="20">
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
