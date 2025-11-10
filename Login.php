
<?php
session_start();

// Evitar ejecución directa
 if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: Login.html");
   exit;
}

try {
    $host = 'localhost';
    $dbname = 'bdbocadillos';
    $user = 'root';
    $pass = '';
    $port = "3307"; 

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    //if (!isset($_POST['RespuestaUsuario']) || !isset($_POST['RespuestaContrasenya'])) {
    //    echo "Faltan datos del formulario.";
    //    exit;
    // }

    $usuario = trim($_POST['RespuestaUsuario']);
    $contrasenya = trim($_POST['RespuestaContrasenya']);

    if (empty($usuario) || empty($contrasenya)) {
        echo "Por favor, completa todos los campos.";
        exit;
    }

    //Para buscar los usuarios en la base de datos
    $sql = "SELECT * FROM usuarios WHERE nombreUsuario = :usuario AND contrasenya = :contrasenya";
    $stmt = $pdo->prepare($sql);

    // Enlaza los parámetros de forma segura para evitar inyecciones SQL.
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $stmt->bindParam(':contrasenya', $contrasenya, PDO::PARAM_STR);
    $stmt->execute();

    //Verifica si se encontró exactamente un usuario.
    if ($stmt->rowCount() === 1) {
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);


        //Guarda la informacion del usuario en la sesion
        $_SESSION['usuario_id'] = $fila['id'];
        $_SESSION['nombreUsuario'] = $fila['nombreUsuario'];
        $_SESSION['rol'] = $fila['rol'];

        $rol = $fila['rol'];

        if ($rol === 'Administrador') {
            header("Location: Administrador.php");
            die();
        } elseif ($rol === 'Cocina') {
            header("Location: PerfilCocina.html");
            die();
        } elseif ($rol === 'Alumno') {
            header("Location: MenuBocadillo.php");
            die();
        } else {
            echo "Rol no reconocido.";
        }
        exit();
    } else {
        echo "Usuario o contraseña incorrectos.";
    }

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>

