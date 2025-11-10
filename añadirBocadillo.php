<?php 

//Conexion con la base de datos
    $host = 'localhost';
    $dbname = 'bdbocadillos';
    $user = 'root';
    $pass = '';
    $port = "3307";
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";


 // Verifica si la solicitud HTTP es de tipo POST 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        //Crea la conexion PDO con la base de datos
        $conexion = new PDO($dsn, $user, $pass);

        //Las excepeciones de la conexión
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        //Los valores del formulario
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];

        // Verifica si se ha enviado el campo 'tipo', si no lo pone  como null.
        $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;


        //Si no se pone el tipo de bocadillo, sale el error
        if (!$tipo) {
            $mensaje = "Por favor selecciona el tipo (frío o caliente).";
        } else {
            //La consulta para añadir un bocadillo
            $sql = "INSERT INTO bocadillos (nombre, descripcion, precio, tipo) VALUES (?, ?, ?, ?)";
            $consulta = $conexion->prepare($sql);

            //Ejecuta la consulta con los valores del form
            $resultado = $consulta->execute([$nombre, $descripcion, $precio, $tipo]);


            if ($resultado) {
                header("Location: Administador.php");
                exit();
            } else {
                //Si en la ejecucion hay un error muestra el error
                $errorInfo = $consulta->errorInfo();
                $mensaje = "Error al agregar el bocadillo: " . $errorInfo[2];
            }
        }
    } catch (PDOException $e) {
        $mensaje = "Error en la conexión o al guardar: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="añadirBocadillo.css">
    <title>Añadir Bocadillo</title>
</head>
<body>

<div id="PseudoFondo">
    <h2>Añadir Bocadillos</h2>
        <form action="añadirBocadillo.php" method="post">
            <label class="TextoInicioSesion" for="nombre">
                Nombre del bocadillo:
            </label>
                <input type="text" name="nombre" required>

            <label class="TextoInicioSesion" for="Descripción">
                Descripción: 
            </label>
            <textarea name="descripcion" id="descripcion" required></textarea>

            <label class="TextoInicioSesion" for="Tipo">
                Tipo: 
            </label>
            <input type="radio" id="caliente" name="tipo" value="caliente" required>
            <label for="caliente">Caliente</label>

            <input type="radio" id="frio" name="tipo" value="frio" required>
            <label for="frio">Frío</label>

            <label class="TextoInicioSesion" for="Precio">
                Precio:
            </label>

                <input type="number" name="precio" placeholder="Precio" required>
                <button id="BotonInicioSesion" type="submit">Agregar Bocadillo</button>
            </form>
        </div>
</div>

</body>
</html>