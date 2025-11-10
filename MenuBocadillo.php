<?php
session_start(); 

if (!isset($_SESSION['usuario_id'])) {
    header("Location: Login.html");
    exit;
}

$mensaje = "";

try {
    $host = 'localhost';
    $dbname = 'bdbocadillos';
    $user = 'root';
    $pass = '';
    $port = "3307";
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $usuario_id = $_SESSION['usuario_id'];

    // Verificar si ya ha pedido hoy
    $sqlCheckHoy = "SELECT COUNT(*) FROM pedidos WHERE usuario_id = :usuario_id AND fecha = CURDATE()";
    $stmtHoy = $pdo->prepare($sqlCheckHoy);
    $stmtHoy->execute([':usuario_id' => $usuario_id]);
    $yaPedidoHoy = $stmtHoy->fetchColumn() > 0;

    // Obtener todos los bocadillos con sus precios
    $sqlBocadillos = "SELECT id, nombre, precio FROM bocadillos";
    $stmtBocadillos = $pdo->query($sqlBocadillos);
    $bocadillos = [];
    while ($row = $stmtBocadillos->fetch(PDO::FETCH_ASSOC)) {
        $bocadillos[$row['id']] = $row;
    }

} catch (PDOException $e) {
    $yaPedidoHoy = false; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $bocadillo_id = $_POST['bocadillo_id'] ?? null;
        $tipo = $_POST['tipo'] ?? null;

        if (!$bocadillo_id || !$tipo) {
            $mensaje = "Datos incompletos.";
        } else {
            // Verificar si el bocadillo_id existe en la tabla bocadillos
            $sqlCheckBocadillo = "SELECT COUNT(*) FROM bocadillos WHERE id = :id";
            $stmtCheckBocadillo = $pdo->prepare($sqlCheckBocadillo);
            $stmtCheckBocadillo->execute([':id' => $bocadillo_id]);

            if ($stmtCheckBocadillo->fetchColumn() == 0) {
                $mensaje = "El bocadillo seleccionado no existe.";
            } else {
                // Verificar si ya ha pedido ese tipo hoy
                $sqlCheck = "SELECT COUNT(*) FROM pedidos WHERE usuario_id = :usuario_id AND tipo = :tipo AND fecha = CURDATE()";
                $stmt = $pdo->prepare($sqlCheck);
                $stmt->execute([':usuario_id' => $usuario_id, ':tipo' => $tipo]);

                if ($stmt->fetchColumn() > 0) {
                    $mensaje = "Ya has pedido un bocadillo $tipo hoy.";
                } else {
                    $sqlInsert = "INSERT INTO pedidos (usuario_id, bocadillo_id, tipo, fecha) VALUES (:usuario_id, :bocadillo_id, :tipo, CURDATE())";
                    $stmtInsert = $pdo->prepare($sqlInsert);
                    $stmtInsert->execute([
                        ':usuario_id' => $usuario_id,
                        ':bocadillo_id' => $bocadillo_id,
                        ':tipo' => $tipo
                    ]);

                    $mensaje = "Pedido de bocadillo $tipo registrado correctamente.";
                    $yaPedidoHoy = true;
                }
            }
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="MenuBocadillo.css">
    <title>Menu Bocadillos</title>
</head>
<body>
    <header>
        <nav id="cabecera">
            <a class="BotonCabecera" href="index.html">Usuario</a>
            <a class="BotonCabecera" href="Login.html">Cerrar Sesión</a>
            <?php echo htmlspecialchars($_SESSION['nombreUsuario']); ?>
        </nav>
    </header>
    <div class="mensaje">

    <?php if ($mensaje): ?>

        <div class="mensaje-aviso">

            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="fila-bocata">

        <div class="bocata">
            <img id="imagenEstado" src="Image/MenuBocadillo/caliente.png" alt="Icono Caliente">
            <img src="Image/MenuBocadillo/bocadilloDeCalamares.jpeg" alt="Bocadillo De Calamares">
            <p class="bocata-info">Bocadillo de Calamares</p>
            <p class="bocata-descripcion">Crujientes calamares rebozados servidos en pan artesanal recién horneado.</p>
            <p class="bocata-precio">Precio: <?= htmlspecialchars($bocadillos[1]['precio'] ?? 'N/A') ?> €</p>

            <div class="imagenAlergenos">
                <img id="imagenAlergenos" src="Image/MenuBocadillo/trigo.png" alt="trigo">
                <img id="imagenAlergenos" src="Image/MenuBocadillo/pescado.png" alt="pescado">
                <img id="imagenAlergenos" src="Image/MenuBocadillo/leche.png" alt="leche">
                <img id="imagenAlergenos" src="Image/MenuBocadillo/huevo.png" alt="Huevo">
            </div>

            <button id="BotonPedirBocadillo" data-id="1" data-tipo="caliente" <?= $yaPedidoHoy ? 'disabled' : '' ?>>
                <?= $yaPedidoHoy ? 'YA HAS PEDIDO HOY' : 'Pedir' ?>
            </button>

            <button id="BotonPedirBocadillo" ></button>
        </div>

        <div class="bocata">
            <img id="imagenEstado" src="Image/MenuBocadillo/frio.png" alt="Icono frio">
            <img src="Image/MenuBocadillo/bocadilloJamonYQueso.jpeg" alt="Bocadillo Jamón Serrano Y Queso">
            <p class="bocata-info">Bocadillo Jamón Serrano y Queso</p>
            <p class="bocata-descripcion">Jamón serrano de calidad con queso curado fundido, todo en un pan rústico.</p>
            <p class="bocata-precio">Precio: <?= htmlspecialchars($bocadillos[6]['precio'] ?? 'N/A') ?> €</p>

            <div class="imagenAlergenos">
                <img id="imagenAlergenos" src="Image/MenuBocadillo/trigo.png" alt="trigo">
                <img id="imagenAlergenos" src="Image/MenuBocadillo/leche.png" alt="leche">
                <img id="imagenAlergenos" src="Image/MenuBocadillo/huevo.png" alt="Huevo">
            </div>

            <button id="BotonPedirBocadillo" data-id="6" data-tipo="caliente" <?= $yaPedidoHoy ? 'disabled' : '' ?>>
                <?= $yaPedidoHoy ? 'YA HAS PEDIDO HOY' : 'Pedir' ?>
            </button>
        </div>

        <!-- Formulario oculto para enviar los datos del pedido -->
        <form id="formularioPedido" method="POST" style="display: none;">
            <input type="hidden" name="bocadillo_id" id="inputBocadilloId">
            <input type="hidden" name="tipo" id="inputTipo">
        </form>

        <script>
            const yaPedidoHoy = <?= $yaPedidoHoy ? 'true' : 'false' ?>;

            document.querySelectorAll('#BotonPedirBocadillo').forEach(button => {
                button.addEventListener('click', function () {
                    if (yaPedidoHoy) {
                        alert("Ya has realizado un pedido hoy.");
                        return;
                    }
                    document.getElementById('inputBocadilloId').value = this.dataset.id;
                    document.getElementById('inputTipo').value = this.dataset.tipo;
                    document.getElementById('formularioPedido').submit();
                });
            });
        </script>
    </div>
</body>
</html>



