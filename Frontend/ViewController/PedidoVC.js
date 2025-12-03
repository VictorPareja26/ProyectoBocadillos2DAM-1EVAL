window.onload = function() {
    cargarPedidos();
};

function cargarPedidos() {

    fetch("../Backend/web_services/sw_pedido.php?accion=listar")
    .then(r => r.json())
    .then(data => {

        if (!data.exito) {
            alert(data.mensaje);
            return;
        }

        let tbody = document.getElementById("tbodyPedidos");
        tbody.innerHTML = "";

        data.datos.forEach(function(p) {

            let boton = "";
            if (p.estado === "Pendiente") {
                boton = "<button onclick='marcarEntregado(" + p.id + ")'>Entregar</button>";
            } else {
                boton = "<span style='color:green;font-size:20px;'>✔</span>";
            }

            let tr = document.createElement("tr");

            tr.innerHTML = `
                <td>${p.id}</td>
                <td>${p.alumno}</td>
                <td>${p.bocadillo}</td>
                <td>${p.tipo}</td>
                <td>${p.fecha}</td>
                <td>${p.estado}</td>
                <td>${boton}</td>
            `;

            tbody.appendChild(tr);
        });
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
    .then(data => {

        if (!data.exito) {
            alert(data.mensaje);
            return;
        }

        cargarPedidos();
    });
}
