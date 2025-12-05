let paginaActual = 1; // Página inicial

window.onload = function () {
    cargarPedidos();
};

function cargarPedidos(operacion = null) {

    if (operacion === "siguiente") paginaActual++;
    if (operacion === "anterior" && paginaActual > 1) paginaActual--;
    if (operacion === "primero") paginaActual = 1;
    if (operacion === "ultimo") paginaActual = 9999;

    let form = new FormData();
    form.append("accion", "listar");
    form.append("pagina", paginaActual);

    fetch("../Backend/web_services/sw_pedido.php", {
        method: "POST",
        body: form
    })
        .then(res => res.json())
        .then(function (response) {

            if (!response.exito) {
                alert("Error cargando pedidos");
                return;
            }

            const tbody = document.getElementById("tbodyPedidos");
            tbody.innerHTML = "";

            let frios = 0, calientes = 0;

            if (response.datos.length > 0) {

                response.datos.forEach(element => {

                    if (element.tipo === "frio") frios++;
                    if (element.tipo === "caliente") calientes++;

                    const tr = document.createElement("tr");

                    // ALUMNO
                    const td_alumno = document.createElement("td");
                    td_alumno.innerText = element.alumno;
                    tr.appendChild(td_alumno);

                    // BOCADILLO
                    const td_bocadillo = document.createElement("td");
                    td_bocadillo.innerText = element.bocadillo;
                    tr.appendChild(td_bocadillo);

                    // TIPO
                    const td_tipo = document.createElement("td");
                    td_tipo.innerText = element.tipo;
                    td_tipo.classList.add(element.tipo);
                    tr.appendChild(td_tipo);

                    // FECHA
                    const td_fecha = document.createElement("td");
                    td_fecha.innerText = element.fecha;
                    tr.appendChild(td_fecha);

                    // ESTADO
                    const td_estado = document.createElement("td");
                    td_estado.innerText = element.estado;
                    td_estado.classList.add(
                        element.estado === "Pendiente"
                            ? "entregaPendiente"
                            : "entregaCompleta"
                    );
                    tr.appendChild(td_estado);

                    // ACCIÓN
                    const td_acciones = document.createElement("td");

                    if (element.estado === "Pendiente") {
                        const botonEntregar = document.createElement("button");
                        botonEntregar.textContent = "Entregar";
                        botonEntregar.onclick = () => marcarEntregado(element.id);
                        td_acciones.appendChild(botonEntregar);
                    } else {
                        td_acciones.innerHTML = `<span style="color:green;font-size:20px;">✔</span>`;
                    }

                    tr.appendChild(td_acciones);
                    tbody.appendChild(tr);
                });
            }

            // Actualizar contadores
            document.getElementById("totalPedidos").innerText = response.total;
            document.getElementById("totalFrios").innerText = frios;
            document.getElementById("totalCalientes").innerText = calientes;

            // Actualizar página
            paginaActual = response.pagina;
            document.getElementById("pagina").value = paginaActual;

            document.getElementById("anterior").disabled = paginaActual <= 1;
            document.getElementById("siguiente").disabled = paginaActual >= response.totalPaginas;
        });
}

function marcarEntregado(id) {
    let form = new FormData();
    form.append("accion", "marcarEntregado");
    form.append("id", id);

    fetch("../Backend/web_services/sw_pedido.php", {
        method: "POST",
        body: form
    })
        .then(res => res.json())
        .then(() => cargarPedidos());
}
