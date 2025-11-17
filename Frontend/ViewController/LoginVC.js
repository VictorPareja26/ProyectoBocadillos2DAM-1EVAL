function iniciarSesion() {
    const usuario = document.getElementById("RespuestaUsuario").value;
    const contrasenya = document.getElementById("RespuestaContrasenya").value;

    fetch('../Backend/web_services/sw_usuario.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            accion: 'login',
            usuario: usuario,
            contrasenya: contrasenya
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            window.location.href = data.pagina;
        } else {
            alert(data.mensaje);
        }
    })
    .catch(() => {
        alert("Error de conexión con el servidor");
    });
}
