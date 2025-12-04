// MenuBocadilloVC.js

function getBocadillos() {

    let datosEnvio = {
        accion: "get"
    };

    fetch('../Backend/web_services/sw_bocadillo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosEnvio)
    })
    .then(response => {
        if (!response.ok) throw new Error('Error de red');
        return response.json();
    })
    .then(data => {
        console.log("Data bocadillos:", data);
        const contenedor = document.getElementById("contenedor_bocadillos");

        if (data.success) {
            if (data.data.length > 0) {
                contenedor.innerHTML = "";
                data.data.forEach(element => {
                    
                    const div = document.createElement("div");
                    div.className = "bocata";

                    // Imagen estado (caliente/frío)
                    const estado = document.createElement("img");
                    estado.src = "Image/MenuBocadillo/" + element.tipo + ".png";
                    estado.alt = element.tipo;
                    div.appendChild(estado);

                    // Imagen bocadillo
                    const img = document.createElement("img");
                    img.src = element.imagen;
                    img.alt = element.nombre;
                    div.appendChild(img);

                    // Nombre
                    const nombre = document.createElement("p");
                    nombre.className = "bocata-info";
                    nombre.innerHTML = element.nombre;
                    div.appendChild(nombre);

                    // Descripción
                    const descripcion = document.createElement("p");
                    descripcion.className = "bocata-descripcion";
                    descripcion.innerHTML = element.descripcion;
                    div.appendChild(descripcion);

                    // Precio
                    const precio = document.createElement("p");
                    precio.className = "bocata-precio";
                    precio.innerHTML = "Precio: " + element.precio + " €";
                    div.appendChild(precio);

                    // Botón pedir
                    const boton = document.createElement("button");
                    boton.innerHTML = data.yaPedidoHoy ? "YA HAS PEDIDO HOY" : "Pedir";
                    boton.disabled = data.yaPedidoHoy;
                    boton.onclick = () => { pedirBocadillo(element.id, element.tipo); };
                    div.appendChild(boton);

                    contenedor.appendChild(div);
                });

                // Mostrar botón cancelar si ya hay pedido
                document.getElementById("contenedorCancelar").style.display = data.yaPedidoHoy ? "block" : "none";

            } else {
                contenedor.innerHTML = "<p>No hay bocadillos disponibles</p>";
            }
        } else {
            contenedor.innerHTML = "<p>Error al cargar bocadillos</p>";
        }
    })
    .catch(error => console.error('Error:', error));
}

function pedirBocadillo(id, tipo) {
    let datosEnvio = {
        accion: "insert",
        bocadillo_id: id,
        tipo: tipo
    };

    fetch('../Backend/web_services/sw_bocadillo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosEnvio)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.msg || "Pedido realizado");
        getBocadillos();
    })
    .catch(error => console.error("Error:", error));
}

function cancelarPedido() {
    let datosEnvio = {
        accion: "cancelar"
    };

    fetch('../Backend/web_services/sw_bocadillo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosEnvio)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.msg || "Pedido cancelado");
        getBocadillos();
    })
    .catch(error => console.error("Error:", error));
}
