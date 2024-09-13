// Espera a que el documento esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    // Obtén el botón de inicio de sesión y el formulario
    var loginBtn = document.getElementById("login-btn");
    var loginForm = document.getElementById("login-form");

    // Agrega un evento de clic al botón de inicio de sesión
    loginBtn.addEventListener("click", function() {
        // Cambia el texto del botón a "Cargando..."
        loginBtn.innerText = "Cargando...";

        // Deshabilita el botón para evitar clics múltiples
        loginBtn.disabled = true;

        // Simula un retraso de 2 segundos antes de enviar el formulario
        setTimeout(function() {
            // Envía el formulario
            loginForm.submit();
        }, 1600); 
    });
});