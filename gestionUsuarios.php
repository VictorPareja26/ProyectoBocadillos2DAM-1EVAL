<?php
// Conexion con la base de datos
$host = 'localhost';
$dbname = 'bdbocadillos';
$user = 'root';
$pass = '';
$port = '3307';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Paginación, 5 por pagina
    $porPagina = 5;
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $pagina = max($pagina, 1);
    $inicio = ($pagina - 1) * $porPagina;

    // Búsqueda
    $busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
    $params = [];

    $sqlCount = "SELECT COUNT(*) FROM usuarios";
    $sql = "SELECT * FROM usuarios";

    if ($busqueda !== '') {
        $sqlCount .= " WHERE nombreUsuario LIKE :buscar";
        $sql .= " WHERE nombreUsuario LIKE :buscar";
        $params[':buscar'] = "%$busqueda%";
    }

    $sql .= " LIMIT :inicio, :porPagina";

    // Contar total registros
    $stmtTotal = $pdo->prepare($sqlCount);
    if ($busqueda !== '') {
        $stmtTotal->bindValue(':buscar', $params[':buscar'], PDO::PARAM_STR);
    }
    $stmtTotal->execute();
    $totalRegistros = $stmtTotal->fetchColumn();
    $totalPaginas = ceil($totalRegistros / $porPagina);

    // Obtener datos paginados
    $stmt = $pdo->prepare($sql);
    if ($busqueda !== '') {
        $stmt->bindValue(':buscar', $params[':buscar'], PDO::PARAM_STR);
    }
    $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
    $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="gestionUsuarios.css" />
    <title>Gestión de Usuarios</title>
</head>
<body>

    <h1>Gestión de Usuarios</h1>
<div>
        <form action="añadirUsuario.php" method="get" style="margin-bottom: 15px;">
            <button class="BotonInicioSesion" type="submit">Agregar Usuario</button>
        </form>

        <form method="get" style="margin-bottom: 20px;">
            <input type="text" name="buscar" placeholder="Buscar usuario..." value="<?= htmlspecialchars($busqueda) ?>" />
            <button type="submit">Buscar</button>
        </form>

        <table class="table_id">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Contraseña</th>
                    <th>Correo</th>
                    <th>Fecha</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($usuarios)): ?>
                    <?php foreach ($usuarios as $fila): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['id']) ?></td>
                            <td><?= htmlspecialchars($fila['nombreUsuario']) ?></td>
                            <td><?= htmlspecialchars($fila['contrasenya']) ?></td>
                            <td><?= htmlspecialchars($fila['correo'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($fila['fecha'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($fila['rol']) ?></td>
                            <td>
                                <a href="editarUsuario.php?id=<?= $fila['id'] ?>">
                                    <img src="./Image/MenuAdministrador/lapiz.png" alt="Editar" width="20" />
                                </a>
                                <a href="eliminarUsuario.php?id=<?= $fila['id'] ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                    <img src="./Image/MenuAdministrador/usuario.png" alt="Eliminar" width="20" />
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No existen registros</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPaginas > 1): ?>
            <div class="botonPaginas">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>&buscar=<?= urlencode($busqueda) ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
</div>
</body>
</html>
