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

function insertUsuario() {

    const nombreUsuario = document.getElementById('nombreUsuario').value;
    const contrasenya = document.getElementById('contrasenya').value;
    const correo = document.getElementById('correo').value;
    const rol = document.getElementById('rol').value;


    // Validar que los campos no estén vacíos
    if (!nombreUsuario || !contrasenya || !correo || !rol) {
        alert("Por favor, completa todos los campos");
        return;
    }

    // Preparar los datos para enviar al servidor
    let datos = {
        accion: "insert",
        id: null,
        nombreUsuario: nombreUsuario,
        contrasenya: contrasenya,
        correo: correo,
        rol: rol,
        fecha: null
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
            window.location.href = "Usuarios.html";
        } else {
            alert("Error: " + (response.msg || "No se pudo insertar el usuario"));
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error de conexión: " + error.message);
    });
}

function modifyUsuario(){
    const id = document.getElementById('id').value;
    const nombreUsuario = document.getElementById('nombreUsuario').value;
    const contrasenya = document.getElementById('contrasenya').value;
    const correo = document.getElementById('correo').value;
    const rol = document.getElementById('rol').value;

    if (!id || !nombreUsuario || !contrasenya || !correo || !rol) {
        alert("Por favor, completa todos los campos");
        return;
    }

    const datos = {
        accion: "update",
        id: id,
        nombreUsuario: nombreUsuario,
        contrasenya: contrasenya,
        correo: correo,
        rol: rol,
        fecha: null
    };

    fetch('../Backend/web_services/sw_usuario.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert(response.msg || "Usuario actualizado correctamente");
            window.location.href = "Usuarios.html";
        } else {
            alert("Error: " + (response.msg || "No se pudo actualizar el usuario"));
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error de conexión: " + error.message);
    });
}

function deleteUsuario(id){
    if (!confirm('¿Estás seguro de eliminar este usuario?')) {
        return;
    }
    
    fetch('../Backend/web_services/sw_usuario.php', {
        method: 'POST',
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            accion: "delete", 
            id: id 
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Usuario eliminado correctamente");
            getUsuarios();
        } else {
            alert("Error: " + data.msg);
        }
    })
    .catch(error => {
        console.error(error);
        alert("Error de conexión");
    });
}

function rellenarCampos(){

    const url = new URL(window.location.href);
    const p_id = url.searchParams.get("id"); 

    if (!p_id) {
        alert('No se especificó un usuario para editar');
        window.location.href = 'Usuarios.html';
        return;
    }

    fetch('../Backend/web_services/sw_usuario.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            accion: "get", 
            id: p_id 
        })
    })
    .then(res => res.json())
    .then(response => {
        if (response.success && response.data) {
            const usuario = response.data;
            
            // Rellenar los campos del formulario
            document.getElementById('id').value = usuario.id || '';
            document.getElementById('nombreUsuario').value = usuario.nombreUsuario || '';
            document.getElementById('contrasenya').value = usuario.contrasenya || '';
            document.getElementById('correo').value = usuario.correo || '';
            document.getElementById('rol').value = usuario.rol || 'Alumno';
        } else {
            alert('No se pudo cargar el usuario');
            window.location.href = 'Usuarios.html';
        }
    })
    .catch(error => {
        console.error("Error al obtener los datos:", error);
        alert('Error al cargar los datos del usuario');
        window.location.href = 'Usuarios.html';
    });
}

function buscarUsuarios() {
    document.getElementById("pagina").value = 1;
    getUsuarios();
}