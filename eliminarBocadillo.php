<?php
session_start();

try {
    $host = 'localhost';
    $dbname = 'bdbocadillos';
    $user = 'root';
    $pass = '';
    $port = "3307";
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mensaje = "";

    // Si se envía un POST para eliminar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
        $eliminar_id = $_POST['eliminar_id'];

        // Consulta para eliminar el bocadillo por id
        $sqlDelete = "DELETE FROM bocadillos WHERE id = :id";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->execute([':id' => $eliminar_id]);

        $mensaje = "Bocadillo eliminado correctamente.";
    }

    // Traer todos los bocadillos para mostrar
    $stmt = $pdo->query("SELECT id, nombre, tipo FROM bocadillos ORDER BY tipo, nombre");
    $bocadillos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $mensaje = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="eliminarBocadillo.css">
<title>Eliminar Bocadillo</title>
</head>
<body>
<div id="PseudoFondo">

<h1>Lista de Bocadillos</h1>

<?php if ($mensaje): ?>
    <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>


        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bocadillos as $b): ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td><?= htmlspecialchars($b['nombre']) ?></td>
                        <td><?= htmlspecialchars($b['tipo']) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este bocadillo?');">
                                <input type="hidden" name="eliminar_id" value="<?= $b['id'] ?>" 
                                />
                                <button id="BotonInicioSesion" type="submit" >
                                    <img src="./Image/MenuAdministrador/usuario.png" alt="Eliminar" width="20"></button>
                            </form>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
</div>
</body>
</html>
