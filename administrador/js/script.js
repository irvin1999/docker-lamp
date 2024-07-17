// Show/hide password onClick of button using Javascript only

// https://stackoverflow.com/questions/31224651/show-hide-password-onclick-of-button-using-javascript-only

function show() {
    var p = document.getElementById('pwd');
    p.setAttribute('type', 'text');
}

function hide() {
    var p = document.getElementById('pwd');
    p.setAttribute('type', 'password');
}


var pwShown = 0;

document.getElementById("eye").addEventListener("click", function () {
    if (pwShown == 0) {
        pwShown = 1;
        show();
    } else {
        pwShown = 0;
        hide();
    }
}, false);

// Nuevo código para mostrar el aviso de usuario inactivo
document.addEventListener("DOMContentLoaded", function () {
    // Obtener el elemento del aviso
    var avisoInactivo = document.getElementById("aviso-inactivo");

    // Comprobar si el aviso debe mostrarse
    if (typeof usuarioInactivo !== "undefined" && usuarioInactivo === true) {
        avisoInactivo.style.display = "block";
    } else {
        avisoInactivo.style.display = "none";
    }

    // Evento para ocultar el aviso si se hace clic en el botón de inicio de sesión
    var botonInicioSesion = document.querySelector(".log-in");
    botonInicioSesion.addEventListener("click", function () {
        avisoInactivo.style.display = "none";
    });
});
