//Agragar Usuario
//Modificar
//Elimninar
function getUsuarios(operacion = null){

    const nombreInput = document.getElementById("nombreUsuario").value;
    let pagina = document.getElementById("pagina");
    let page = pagina.value;
    let paginaNumero = parseInt(page);

    // Manejar las operaciones de paginación
    if (operacion == "siguiente") {
        paginaNumero++;
        pagina.value = paginaNumero;
    }
    if (operacion == "anterior") {
        if (paginaNumero > 1) {
            paginaNumero--;
            pagina.value = paginaNumero;
        }
    }
    if (operacion == "ultimapagina") {
        let pagesInput = document.getElementById("pages");
        paginaNumero = parseInt(pagesInput.value);
        pagina.value = paginaNumero;
    }
    if (operacion == "primerapagina") {
        paginaNumero = 1;
        pagina.value = paginaNumero;
    }

    let datosEnvio = {
        nombreUsuario: nombreInput,
        pagina: paginaNumero,
        accion: "get"
    };

    fetch('../Backend/web_services/sw_usuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosEnvio)
    })
        .then(response => {
            if (!response.ok) throw new Error('Error de red');
            return response.json();
        })
        .then(data => {
            console.log("Data: ", data);
            const tbody = document.getElementById("cuerpo_tabla");
            const input_siguiente = document.getElementById("siguiente");

            if (data.success) {
                if (data.data.length > 0) {
                    tbody.innerHTML = "";
                    data.data.forEach(element => {
                        const tr = document.createElement("tr");

                        const td_id = document.createElement("td");
                        td_id.innerHTML = element.id;
                        tr.appendChild(td_id);

                        const td_nombre = document.createElement("td");
                        td_nombre.innerHTML = element.nombreUsuario;
                        tr.appendChild(td_nombre);

                        const td_contrasenya = document.createElement("td");
                        td_contrasenya.innerHTML = element.contrasenya;
                        tr.appendChild(td_contrasenya);

                        const td_correo = document.createElement("td");
                        td_correo.innerHTML = element.correo ?? '-';
                        tr.appendChild(td_correo);

                        const td_fecha = document.createElement("td");
                        td_fecha.innerHTML = element.fecha ?? '-';
                        tr.appendChild(td_fecha);

                        const td_rol = document.createElement("td");
                        td_rol.innerHTML = element.rol;
                        tr.appendChild(td_rol);

                        const td_acciones = document.createElement("td");

                        const editar = document.createElement("img");
                        editar.src = "./Image/MenuAdministrador/lapiz.png";
                        editar.width = 20;
                        editar.onclick = () => { window.open('editarUsuario.html?id=' + element.id); };

                        const eliminar = document.createElement("img");
                        eliminar.src = "./Image/MenuAdministrador/usuario.png";
                        eliminar.width = 20;
                        eliminar.onclick = () => { deleteUsuario(element.id); };

                        td_acciones.appendChild(editar);
                        td_acciones.appendChild(eliminar);
                        tr.appendChild(td_acciones);

                        tbody.appendChild(tr);
                    });

                    // Actualizar la página actual
                    pagina.value = data.current_page;

                    // Guardar el total de páginas en el hidden input
                    document.getElementById("pages").value = data.pages;

                    // Controlar botón siguiente
                    input_siguiente.disabled = data.current_page >= data.pages;

                } else {
                    input_siguiente.disabled = true;
                }
            } else {
                input_siguiente.disabled = true;
            }
        })
        .catch(error => console.error('Error:', error));
}

function insertUsuario(){
    const id = document.getElementById("id").value;
    const nombreUsuario = document.getElementById("nombreUsuario").value;
    const contrasenya = document.getElementById("contrasenya").value;

    // Preparamos los datos
    let datos = {
        accion: "insert",
        id: id,
        nombreUsuario: nombreUsuario,
        contrasenya: contrasenya
    };

    fetch('../Backend/web_services/sw_usuario.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    })
    .then(response => {
        if (!response.ok) throw new Error("Error en la petición");
        return response.json();
    })
    .then(response => {
        if (response.success) {
            alert(response.msg || "Usuario insertado correctamente");
        } else {
            alert("Error: " + (response.msg || "No se pudo insertar el usuario"));
        }
    })
    .catch(error => console.error("Error:", error));


}

function modifyUsuario(){
}

function deleteUsuario(){
    fetch('../Backend/web_services/sw_usuario.php', {
        method: 'post',
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accion: "delete", id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Usuario eliminado correctamente");
        } else {
            alert("Error: " + data.msg);
        }
    })
    .catch(error => console.error(error));

}

function rellenarCampos(){
     // Obtener el DNI de la dirección de la página
    const urlCompleta = window.location.href;
    const url = new URL(urlCompleta);
    const params = url.searchParams;
    const p_id= params.get("id");

    console.log("ID encontrado:", p_dni);

    //Encontrar los botones en la página
    const botonInsertar = document.getElementById("insert");
    const botonModificar = document.getElementById("update");

    //Decidir si rellenamos el formulario o no
    if (p_id !== null) {

        botonInsertar.hidden = true;
        botonModificar.hidden = false;

        //  Pedir los datos del alumno al servidor usando Fetch
        fetch('http://localhost:8080/DI/sw_usuario.php?id=' + p_id, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ accion: "get", dni: p_id }) 
        })
        .then(respuesta => {
            if (!respuesta.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return respuesta.json();
        })
        .then(response => {
            // Validar que response.data existe y es un array
            if (response && Array.isArray(response.data) && response.data.length > 0) {
                const usuario = response.data.find(a => a.id === p_id);
                
                // Validar que se encontró el alumno
                if (usuario) {
                    document.getElementById('id').value = usuario.id || '';
                    document.getElementById('nombreUsuario').value = usuario.nombreUsuario || '';
                } else {
                    console.log('No se encontró el alumno con ID:', p_id);
                }
            } else {
                console.log('No se encontraron datos para el ID:', p_id);
            }
        })
        .catch(error => {
            console.error("Algo salió mal al obtener los datos:", error);
        });

    } else {

        // Si no hay un ID, estamos creando un nuevo alumno
        botonInsertar.hidden = false;
        botonModificar.hidden = true;
    }

}