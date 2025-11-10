<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'bdbocadillos';
$user = 'root';
$pass = '';
$port = '3307';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Comprobamos si se ha enviado el formulario
if (isset($_POST['nombreUsuario']) && isset($_POST['contrasenya']) && isset($_POST['correo']) && isset($_POST['rol'])) {

    $nombreUsuario = $_POST['nombreUsuario'];
    $contrasenya = $_POST['contrasenya'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    // Insertar en la base de datos
    $sql = "INSERT INTO usuarios (nombreUsuario, contrasenya, correo, rol, fecha) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombreUsuario, $contrasenya, $correo, $rol]);

    // Redirigir después de añadir un usuario nuevo
    header("Location: gestionDeUsuarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Añadir Usuario</title>
    <link rel="stylesheet" href="añadirUsuario.css">
</head>

<body>

    <header>
        <form action="gestionDeUsuarios.php" method="get">
            <button class="BotonCabecera" type="submit">Volver Atrás</button>
        </form>

    </header>

    <div id="PseudoFondo">

        <h1>Añadir Nuevo Usuario</h1>

        <form action="añadirUsuario.php" method="post">
            <label for="nombreUsuario">Nombre de Usuario:</label><br>
            <input type="text" name="nombreUsuario" required><br><br>

            <label for="contrasenya">Contraseña:</label><br>
            <input type="password" name="contrasenya" required><br><br>

            <label for="correo">Correo:</label><br>
            <input type="email" name="correo" required><br><br>

            <label for="rol">Rol:</label><br>
            <select name="rol" required>
                <option value="cocina">cocina</option>
                <option value="alumno">alumno</option>
                <option value="admin">Administrador</option>

            </select>

            <button id="BotonInicioSesion" type="submit">Añadir Usuario</button>
    </div>
    </form>
    </div>
</body>

</html>