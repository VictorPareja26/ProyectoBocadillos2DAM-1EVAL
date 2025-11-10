<?php
$host = 'localhost';
$dbname = 'bdbocadillos';
$user = 'root';
$pass = '';
$port = "3307";
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$bocadillo = null;
$mensaje = '';

// Cargar bocadillo si viene id en GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idSeleccionado = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM bocadillos WHERE id = ?");
    $stmt->execute([$idSeleccionado]);
    $bocadillo = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Si se envía un formulario POST para actualizar un bocadillo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    //Cogemos los datos del formulario
    $id = (int) $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $precio = trim($_POST['precio']);
    $descripcion = trim($_POST['descripcion']);
    $tipo = $_POST['tipo'];


    // Se valida que los campos obligatorios están llenos y que el tipo es válido

    if ($id > 0 && $nombre !== '' && $precio !== '' && $descripcion !== '' && ($tipo === 'frio' || $tipo === 'caliente')) {
        $stmt = $pdo->prepare("UPDATE bocadillos SET nombre = ?, precio = ?, descripcion = ?, tipo = ? WHERE id = ?");
        $stmt->execute([$nombre, $precio, $descripcion, $tipo, $id]);
        $mensaje = "Registro actualizado correctamente.";

        // Se actualizan los datos
        $stmt = $pdo->prepare("SELECT * FROM bocadillos WHERE id = ?");
        $stmt->execute([$id]);
        $bocadillo = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $mensaje = "Por favor, rellena todos los campos correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="editarBocadillo.css">
<title>Editar Bocadillo</title>
</head>
<body>
    <div id="PseudoFondo">

        <?php if ($mensaje): ?>
            <div class="mensaje">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <?php if (!$bocadillo): ?>
            <h2>Selecciona un bocadillo para editar:</h2>
            <form  class="buscadorYaniadir"method="POST" action="">
                <div id="FormularioInicio">
                    <label class="TextoInicioSesion">
                        <select name="select_bocadillo" onchange="this.form.submit()">
                            <option value="">-- Selecciona un bocadillo --</option>
                            <?php foreach ($todosBocadillos as $b): ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= htmlspecialchars($b['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <noscript><button type="submit" name="cargar">Cargar</button></noscript>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($bocadillo): ?>
            <h2>Editar bocadillo: <?= htmlspecialchars($bocadillo['nombre']) ?></h2>
            <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= $bocadillo['id'] ?>">

                    <label class="TextoInicioSesion">
                        Nombre:
                        <input type="text" name="nombre" value="<?= htmlspecialchars($bocadillo['nombre']) ?>" required>
                    </label>
                    <br>

                    <label class="TextoInicioSesion">
                        Precio:
                        <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($bocadillo['precio']) ?>" required>
                    </label>
                    <br>

                    <label class="TextoInicioSesion">
                        Descripción:
                        <textarea name="descripcion" rows="3" required><?= htmlspecialchars($bocadillo['descripcion']) ?></textarea>
                    </label>
                    <br>

                    <label class="TextoInicioSesion">
                        Tipo:
                        <select name="tipo" required>
                            <option value="frio" <?= $bocadillo['tipo'] === 'frio' ? 'selected' : '' ?>>Frío</option>
                            <option value="caliente" <?= $bocadillo['tipo'] === 'caliente' ? 'selected' : '' ?>>Caliente</option>
                        </select>
                    </label>
                    <br>

                    <button id="BotonInicioSesion" type="submit" name="actualizar">Actualizar</button>
                </div>
            </form>
        <?php endif; ?>

    </div>
</body>
</html>



