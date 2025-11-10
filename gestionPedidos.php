<?php
// Conexion DB
$host = 'localhost';
$dbname = 'bdbocadillos';
$user = 'root';
$pass = '';
$port = '3307';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mensaje = "";

    // Eliminar pedido (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
        $eliminar_id = $_POST['eliminar_id'];

        $sqlDelete = "DELETE FROM pedidos WHERE id = :id";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->execute([':id' => $eliminar_id]);

        $mensaje = "Pedido cancelado correctamente.";
    }

    // Paginación y búsqueda
    $porPagina = 5;
    $pagina = isset($_GET['pagina']) ? max((int)$_GET['pagina'], 1) : 1;
    $inicio = ($pagina - 1) * $porPagina;

    $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

    // La consulta para contar total registros con búsqueda, para ver las paginas que se necesitan
    $sqlCount = "SELECT COUNT(*) 
                FROM pedidos p 
                JOIN usuarios u ON p.usuario_id = u.id";
    //Lo que se va ha ver en la pagina
    $sql = "SELECT p.id, u.nombreUsuario AS cliente, p.fecha, p.tipo AS estado 
            FROM pedidos p 
            JOIN usuarios u ON p.usuario_id = u.id";

    //Los datos se almacenan aqui
    $params = [];
    //Si has buscado algo que no sea espacios, se agraga la condicion
    if ($buscar !== '') {
        $sqlCount .= " WHERE u.nombreUsuario LIKE :buscar";
        $sql .= " WHERE u.nombreUsuario LIKE :buscar";
        $params[':buscar'] = "%$buscar%";
    }

    $sql .= " ORDER BY p.fecha DESC LIMIT :inicio, :porPagina";

    // Ejecutar el conteo
    $stmtCount = $pdo->prepare($sqlCount);
    if ($buscar !== '') {
        $stmtCount->bindValue(':buscar', $params[':buscar'], PDO::PARAM_STR);
    }
    $stmtCount->execute();
    $totalRegistros = $stmtCount->fetchColumn();
    $totalPaginas = ceil($totalRegistros / $porPagina);

    // Obtener registros paginados
    $stmt = $pdo->prepare($sql);
    if ($buscar !== '') {
        $stmt->bindValue(':buscar', $params[':buscar'], PDO::PARAM_STR);
    }
    $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
    $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="gestionPedidos.css" />
</head>
<body>

<h1>Gestión de Pedidos</h1>

<?php if ($mensaje !== ""): ?>
    <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

        
<div>
    <form method="get" class="buscador">
        <input type="text" name="buscar" placeholder="Buscar por cliente..." value="<?= htmlspecialchars($buscar) ?>" />
        <button type="submit">Buscar</button>
    </form>


    <form action="realizarPedido.php" method="get" style="margin-bottom: 15px;">
            <button class="BotonInicioSesion" type="submit">Realizar Pedido</button>
    </form>

    <table class="table_id">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($pedidos) > 0): ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= htmlspecialchars($pedido['id']) ?></td>
                        <td><?= htmlspecialchars($pedido['cliente']) ?></td>
                        <td><?= htmlspecialchars($pedido['fecha']) ?></td>
                        <td><?= htmlspecialchars($pedido['estado']) ?></td>
                        <td>
                            <a href="editarPedido.php?id=<?= $pedido['id'] ?>">
                                <img src="./Image/MenuAdministrador/lapiz.png" alt="Editar" width="20" />
                            </a>
                            <form method="post" class="celdaAcciones" onsubmit="return confirm('¿Seguro que quieres eliminar este pedido?');">
                                <input type="hidden" name="eliminar_id" value="<?= $pedido['id'] ?>">
                                <button type="submit" class="botonEliminar">
                                    <img src="./Image/MenuAdministrador/usuario.png" alt="Eliminar" width="20" />
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No se encontraron pedidos.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="botonPaginas">
        <?php if ($pagina > 1): ?>
            <a href="?pagina=<?= $pagina - 1 ?>&buscar=<?= urlencode($buscar) ?>">&lt;</a>
        <?php endif; ?>

        <?php for ($i=1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>&buscar=<?= urlencode($buscar) ?>" class="<?= ($i === $pagina) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($pagina < $totalPaginas): ?>
            <a href="?pagina=<?= $pagina + 1 ?>&buscar=<?= urlencode($buscar) ?>">&gt;</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>



