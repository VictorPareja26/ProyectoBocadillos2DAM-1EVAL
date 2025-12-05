window.onload = function() {
    cargarPedidos();
};

function cargarPedidos() {

    fetch("../Backend/web_services/sw_pedido.php?accion=listar")
        .then(res => res.json())
        .then(function (response) {

            if (!response.exito) {
                alert("Error cargando pedidos");
                return;
            }

            const tbody = document.getElementById("tbodyPedidos");
            tbody.innerHTML = "";

            let total = 0, frios = 0, calientes = 0;

            if (response.datos.length > 0) {

                response.datos.forEach(element => {

                    total++;
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

            document.getElementById("totalPedidos").innerText = total;
            document.getElementById("totalFrios").innerText = frios;
            document.getElementById("totalCalientes").innerText = calientes;
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
