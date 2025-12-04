window.onload = function() {
    cargarPedidos();
};

function cargarPedidos() {

    fetch("../Backend/web_services/sw_pedido.php?accion=listar")
    .then(r => r.json())
    .then(data => {

        let tbody = document.getElementById("tbodyPedidos");
        tbody.innerHTML = "";

        let total = 0, frios = 0, calientes = 0;

        data.datos.forEach(function(p) {

            total++;
            if (p.tipo === "frio") frios++;
            if (p.tipo === "caliente") calientes++;

            let estadoClass = (p.estado === "Pendiente") ? "entregaPendiente" : "entregaCompleta";

            let boton = (p.estado === "Pendiente")
            ? `<button onclick="marcarEntregado(${p.id})">Entregar</button>`
            : `<span style='color:green;font-size:20px;'>✔</span>`;

            let tr = `
                <tr>
                    <td>${p.alumno}</td>
                    <td>${p.bocadillo}</td>
                    <td class="${p.tipo}">${p.tipo}</td>
                    <td>${p.fecha}</td>
                    <td class="${estadoClass}">${p.estado}</td>
                    <td>${boton}</td>
                </tr>
            `;

            tbody.innerHTML += tr;
        });

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
    .then(r => r.json())
    .then(() => cargarPedidos());
}
