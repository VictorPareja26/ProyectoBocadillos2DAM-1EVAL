<?php
session_start();

//Conexio con  la base de datos

$host = 'localhost';
$dbname = 'bdbocadillos';
$user = 'root';
$pass = '';
$port = "3307";
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener todos los pedidos
$pedidos = $pdo->query("SELECT * FROM pedidos")->fetchAll(PDO::FETCH_ASSOC);

// Obtener todos los bocadillos
$todosBocadillos = $pdo->query("SELECT * FROM bocadillos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="editarPedido.css">
    <title>Editar Pedido</title>
</head>
<body>

    <div id="PseudoFondo">


        <h2>Editar Pedido</h2>
        <?php foreach ($pedidos as $pedido): ?>
            <div>

                <form method="post" style="margin-bottom: 20px;">
                        

                    <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                    Usuario ID: <input type="number" name="usuario_id" value="<?= $pedido['usuario_id'] ?>" required><br>

                    Bocadillo:
                    <select name="bocadillo_id" required>
                        <option value="">-- Selecciona un bocadillo --</option>
                        <?php foreach ($todosBocadillos as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $pedido['bocadillo_id'] == $b['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    Tipo:
                    <select name="tipo">
                        <option value="caliente" <?= $pedido['tipo'] == 'caliente' ? 'selected' : '' ?>>Caliente</option>
                        <option value="frio" <?= $pedido['tipo'] == 'frio' ? 'selected' : '' ?>>Frío</option>
                    </select><br>

                    <button id="BotonInicioSesion" type="submit">Actualizar</button>
                </form>
            </div>
        <?php endforeach; ?>
</body>
</html>