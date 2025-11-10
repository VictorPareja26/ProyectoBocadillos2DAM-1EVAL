<?php
// Datos conexión
$host = 'localhost';
$dbname = 'bdbocadillos';
$user = 'root';
$pass = '';
$port = '3307';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8;port=$port";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Esto pertenece al buscador, que los busca en la tabla por nombre de usuario.
    //Guarda el nombre que sea escrito, para buscar.
    $buscar = isset($_POST['buscar']) ? $_POST['nombreUsuario'] : (isset($_POST['nombreUsuario']) ? $_POST['nombreUsuario'] : '');

    // Número filas por página
    $total_rows = 5;

    //Determina la pagina 
    $pagina = isset($_POST["pagina"]) ? (int) $_POST["pagina"] : 1;

    //La primera pagina es la 1
    if (isset($_POST["primera"]))
        $pagina = 1;
    //Se va sumando de 1 en 1
    if (isset($_POST["siguiente"]))
        $pagina++;
    //Si la variable pagina es mayor que 1, se le resta 1, entonces vuelve a la aanterior
    if (isset($_POST["anterior"]) && $pagina > 1)
        $pagina--;

    //Realiza la búsqueda o el conteo total, dependiendo si se ha escrito algo en el campo
    if ($buscar != '') {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios WHERE nombreUsuario LIKE ?");
        $stmt->execute(["%$buscar%"]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    }

    // Obtenemos total registros
    $row = $stmt->fetch();
    $total_alumnos = $row['total'];

    //Cuenta cuantas paginas hay en total entre el total de alumnos, que se ha calculado anteriormente
    // y el total de columnas que se ha declarado arriba
    $total_paginas = ceil($total_alumnos / $total_rows);

    //Va a la ultima pagina
    if (isset($_POST["ultima"]))
        $pagina = $total_paginas;

    // Esto evita que intente acceder, a la página 10 cuando solo existen 5.
    if ($pagina > $total_paginas)
        $pagina = $total_paginas;
    // Así se asegura que la primera página sea la mínima permitida.
    if ($pagina < 1)
        $pagina = 1;

    //Calcula desde que fila empieza a leer    
    $offset = ($pagina - 1) * $total_rows;

    //Consulta para limitar, a la hora de mostrar los usuarios
    if ($buscar != '') {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombreUsuario LIKE ? LIMIT ? OFFSET ?");
        $stmt->bindValue(1, "%$buscar%", PDO::PARAM_STR);
        $stmt->bindValue(2, $total_rows, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $total_rows, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    }

    //Ejecuta la consulta y guarda los resultados
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

    <header>
        <form action="Administrador.php" method="get">
            <button class="BotonCabecera" type="submit">Volver Atrás</button>
        </form>
    </header>

    <h1>Gestión de Usuarios</h1>

    <div>
        <form action="añadirUsuario.php" method="get">
            <button class="BotonInicioSesion" type="submit">Agregar Usuario</button>
        </form>

        <!--Al hacer clic en Buscar, el formulario envía datos por POST, 
        y entonces ese bloque de código se activa. -->
        <form method="POST" action="">
            <input class="buscador" type="text" name="nombreUsuario" placeholder="Escribe nombre"
                value="<?php echo htmlspecialchars($buscar); ?>">
            <button type="submit" name="buscar">Buscar</button>
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
                            <td><?php echo htmlspecialchars($fila['id']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombreUsuario']); ?></td>
                            <td><?php echo htmlspecialchars($fila['contrasenya']); ?></td>
                            <td><?php echo htmlspecialchars($fila['correo'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($fila['fecha'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($fila['rol']); ?></td>
                            <td>
                                <a href="editarUsuario.php?id=<?php echo $fila['id']; ?>">
                                    <img src="./Image/MenuAdministrador/lapiz.png" alt="Editar" width="20" />
                                </a>
                                <a href="eliminarUsuario.php?id=<?php echo $fila['id']; ?>"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
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
    </div>

    <form method="POST" action="">
        <input type="hidden" name="nombreUsuario" value="<?php echo htmlspecialchars($buscar); ?>">
        <input type="hidden" name="pagina" value="<?php echo $pagina; ?>">
        <input class="BotonInicioSesion" type="submit" value="Primera" name="primera">
        <input class="BotonInicioSesion" type="submit" value="Anterior" name="anterior">
        <input class="BotonInicioSesion" type="submit" value="Siguiente" name="siguiente">
        <input class="BotonInicioSesion" type="submit" value="Última" name="ultima">
    </form>

    <div id="textoPagina">
        Página <?php echo $pagina; ?> de <?php echo $total_paginas; ?> | Total usuarios: <?php echo $total_alumnos; ?>
    </div>

</body>

</html>