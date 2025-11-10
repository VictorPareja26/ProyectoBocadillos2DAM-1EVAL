<?php
$host = 'localhost';
$dbname = 'bdbocadillos';
$user = 'root';
$pass = '';
$port = "3307";
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";

$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// Verifica si la solicitud HTTP es de tipo GET y si se ha proporcionado un parámetro 'id'
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Usuario no encontrado.";
        exit;
    }
}

// Ahora, si el formulario envió datos para modificar (viene por POST)
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $nombreUsuario = $_POST['nombreUsuario'];
    $contrasenya = $_POST['contrasenya'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    $stmt = $pdo->prepare("UPDATE usuarios SET nombreUsuario = ?, contrasenya = ?, correo = ?, rol = ? WHERE id = ?");
    $stmt->execute([$nombreUsuario, $contrasenya, $correo, $rol, $id]);

    echo "Usuario modificado correctamente.";
    header("Location: gestionDeUsuarios.php");
    die();

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="editarUsuario.css">
    <title>Editar Usuario</title>
</head>

<body>
    <div id="PseudoFondo">
        <h2>Editar Usuario</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

            <label class="TextoInicioSesion">
                Nombre: <input type="text" name="nombreUsuario"
                    value="<?php echo htmlspecialchars($usuario['nombreUsuario']); ?>">
            </label>
            <br>

            <label class="TextoInicioSesion">
                Contraseña: <input type="password" name="contrasenya"
                    value="<?php echo htmlspecialchars($usuario['contrasenya']); ?>">
            </label>
            <br>

            <label class="TextoInicioSesion">
                Correo: <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>">
            </label>
            <br>

            <label class="TextoInicioSesion">Rol:
                <select name="rol">
                    <option value="Alumno" <?php if ($usuario['rol'] == 'Alumno') {
                        echo 'selected';
                    } ?>>Alumno</option>
                    <option value="Cocina" <?php if ($usuario['rol'] == 'Cocina') {
                        echo 'selected';
                    } ?>>Cocina</option>
                    <option value="Administrador" <?php if ($usuario['rol'] == 'Administrador') {
                        echo 'selected';
                    } ?>>
                        Administrador</option>

                </select>
            </label>
            <br>

            <button id="BotonInicioSesion" type="submit">Actualizar</button>
        </form>
    </div>
</body>

</html>