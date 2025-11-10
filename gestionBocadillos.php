<?php
session_start();

// Conexion a la base de datos
$host = 'localhost';
$dbname = 'bdbocadillos';
$user = 'root';
$pass = '';
$port = '3307';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Verificamos si se han recibido datos del formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
        $idEliminar = (int) $_POST['eliminar_id'];

        if ($idEliminar > 0) {
            try {
                $stmtDelete = $pdo->prepare("DELETE FROM bocadillos WHERE id = ?");
                $stmtDelete->execute([$idEliminar]);

                $_SESSION['mensaje'] = "Bocadillo eliminado correctamente.";
                $_SESSION['mensaje_tipo'] = "exito";

                // Redirige al mismo archivo con los valores actuales de 'pagina' y 'buscar'.
                header("Location: " . $_SERVER['PHP_SELF'] . "?pagina=" . ($_GET['pagina'] ?? 1) . "&buscar=" . urlencode($_GET['buscar'] ?? ''));
                exit();
            } catch (PDOException $e) {
                $_SESSION['mensaje'] = "Error al eliminar bocadillo: " . $e->getMessage();
                $_SESSION['mensaje_tipo'] = "error";

                header("Location: " . $_SERVER['PHP_SELF'] . "?pagina=" . ($_GET['pagina'] ?? 1) . "&buscar=" . urlencode($_GET['buscar'] ?? ''));
                exit();
            }
        }
    }

    // Parámetros paginación y búsqueda para bocadillos
    $porPagina = 5;
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    //Para que pagina no sea menor que 1
    $pagina = max($pagina, 1);
    //Para ver desde que pagina, se empieza a mostrar
    $inicio = ($pagina - 1) * $porPagina;
    
    $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

    $sqlCount = "SELECT COUNT(*) FROM bocadillos";
    $sql = "SELECT * FROM bocadillos";

    $params = [];
    if ($buscar !== '') {
        $sqlCount .= " WHERE nombre LIKE :buscar";
        $sql .= " WHERE nombre LIKE :buscar";
        $params[':buscar'] = "%$buscar%";
    }
    // Agrega la cláusula LIMIT para paginar resultados (inicio, cantidad)

    $sql .= " LIMIT :inicio, :porPagina";

    // Preparar y ejecutar consulta para contar total de registros que cumplen los requisitos
    $stmtTotal = $pdo->prepare($sqlCount);
    if ($buscar !== '') {
        $stmtTotal->bindValue(':buscar', $params[':buscar'], PDO::PARAM_STR);
    }
    $stmtTotal->execute();
    $totalRegistros = $stmtTotal->fetchColumn();

    // Calcula cuántas páginas hay en total
    $totalPaginas = ceil($totalRegistros / $porPagina);

    // Preparar consulta para obtener los bocadillos según filtros y paginación
    $stmt = $pdo->prepare($sql);
    if ($buscar !== '') {
        $stmt->bindValue(':buscar', $params[':buscar'], PDO::PARAM_STR);
    }

    // Bind de parámetros para LIMIT deben ser enteros
    $stmt->bindValue(':inicio', (int)$inicio, PDO::PARAM_INT);
    $stmt->bindValue(':porPagina', (int)$porPagina, PDO::PARAM_INT);
    $stmt->execute();

    $bocadillos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Recuperar mensaje y limpia la  sesión
$mensaje = $_SESSION['mensaje'] ?? '';
$mensaje_tipo = $_SESSION['mensaje_tipo'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="gestionBocadillo.css" />
<title>Gestión Bocadillos</title>
</head>
<body>
    <h1>Gestión Bocadillos</h1>
<div>


        <?php if ($mensaje): ?>
            <div class="mensaje <?= htmlspecialchars($mensaje_tipo) ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <form method="get" class="buscadorYaniadir">
            <input type="text" name="buscar" placeholder="Buscar bocadillos por nombre..." value="<?= htmlspecialchars($buscar) ?>" />
            <button type="submit">Buscar</button>
        </form>

        <form action="añadirBocadillo.php" method="get" class="buscadorYaniadir">
            <button class="BotonInicioSesion" type="submit">Añadir Bocadillo</button>
        </form>

    <table class="table_id">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($bocadillos)): ?>
                <?php foreach ($bocadillos as $bocadillo): ?>
                    <tr>
                        <td><?= htmlspecialchars($bocadillo['id']) ?></td>
                        <td><?= htmlspecialchars($bocadillo['nombre']) ?></td>
                        <td><?= htmlspecialchars($bocadillo['precio']) ?> €</td>
                        <td><?= htmlspecialchars($bocadillo['descripcion']) ?></td>
                        <td>
                            <a href="editarBocadillo.php?id=<?= $bocadillo['id'] ?>">
                                <img src="./Image/MenuAdministrador/lapiz.png" alt="Editar" width="20" />
                            </a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('¿Seguro que quieres eliminar este Bocadillo?');">
                                <input type="hidden" name="eliminar_id" value="<?= $bocadillo['id'] ?>">
                                <button type="submit" style="background:none; border:none; padding:0; cursor:pointer;">
                                    <img src="./Image/MenuAdministrador/usuario.png" alt="Eliminar" width="20" />
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No se encontraron bocadillos</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="botonPaginas">
        <?php if ($totalPaginas > 1): ?>
            <div >
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>&buscar=<?= urlencode($buscar) ?>" <?= $i === $pagina ? 'style="font-weight:bold;"' : '' ?>>
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

