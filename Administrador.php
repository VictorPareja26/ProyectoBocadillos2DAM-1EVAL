<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="AdministradorEstilo.css" />
    <title>Panel de Administración</title>
</head>

<body>

 <header>
        <form action="login.html" method="get">
            <button class="BotonCabecera" type="submit">Volver Atrás</button>
        </form>
    </header>

    <h1>Panel de Administración</h1>

    <div class="ContenedorSecciones">
        <section class="PseudoFondo">
            <h2>Descuentos</h2>

            <form method="get">
                <button class="BotonInicioSesion" type="submit">Gestión Descuentos</button>
            </form>

        </section>

        <section class="PseudoFondo">
            <h2>Bocadillos</h2>
            <form action="gestionBocadillos.php" method="get">
                <button class="BotonInicioSesion" type="submit">Gestión Bocadillo</button>
            </form>
        </section>

        <section class="PseudoFondo">
            <h2>Pedidos</h2>
            <form action="gestionPedidos.php" method="get">
                <button class="BotonInicioSesion" type="submit">Gestión Pedido</button>
            </form>
        </section>

        <section class="PseudoFondo">
            <h2>Usuarios</h2>
            <form action="gestionDeUsuarios.php" method="get">
                <button class="BotonInicioSesion" type="submit">Gestión Usuarios</button>
            </form>
        </section>
    </div>
</body>

</html>