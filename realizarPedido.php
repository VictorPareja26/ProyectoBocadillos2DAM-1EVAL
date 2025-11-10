<?php
 $host = 'localhost';
    $dbname = 'bdbocadillos';
    $user = 'root';
    $pass = '';
    $port = "3307"; 

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener todos los usuarios
$usuarios = $pdo->query("SELECT id, nombreUsuario FROM usuarios ORDER BY nombreUsuario")->fetchAll(PDO::FETCH_ASSOC);

//Obtengo los bocadillos
$frio = $pdo->query("SELECT id, nombre FROM bocadillos WHERE tipo = 'frio'")->fetchAll(PDO::FETCH_ASSOC);
$caliente = $pdo->query("SELECT id, nombre FROM bocadillos WHERE tipo = 'caliente'")->fetchAll(PDO::FETCH_ASSOC);


$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = (int) $_POST['usuario_id'];

    if ($usuario_id > 0) {
        // Aquí puedes hacer lo que quieras con el ID seleccionado, como guardar un pedido
        $mensaje = " Usuario seleccionado correctamente: ID $usuario_id";
    } else {
        $mensaje = " Selecciona un usuario válido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="realizarPedido.css">
    <title>Realizar Pedido</title>
</head>
<body>
    <div id="PseudoFondo">

    <h2>Realizar Pedido</h2>

    <?php if (!empty($mensaje)) echo "<p>$mensaje</p>"; ?>

    <form method="POST" action="">

    <label>Selecciona un usuario:</label><br>
        <select name="usuario_id" required>
            <option value="">-- Selecciona un usuario --</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['nombreUsuario']) ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <br>
        <label>Selecciona un bocadillo:</label><br>
        <select name="bocadillo_id" required>
            <option value="">-- Fríos --</option>
            <?php foreach ($frio as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nombre']) ?></option>
            <?php endforeach; ?>
            <option value="">-- Calientes --</option>
            <?php foreach ($caliente as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <br>
        <br>
        <button id="BotonInicioSesion" type="submit">Hacer pedido</button>
    </form>
</body>
</html>

