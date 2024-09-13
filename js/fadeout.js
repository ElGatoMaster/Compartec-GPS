$(document).ready(function() {
    $('[name="fade"]').click(function(event) {
        // Detener el comportamiento predeterminado del enlace
        event.preventDefault();

        // Fijar la altura del contenido principal para evitar colapsos durante el fadeOut
        $('body, .container-fluid').css('height', $('body, .container-fluid').height());
        $('.flex-shrink-0').css('height', $('.flex-shrink-0').height());

        // Obtener la URL del enlace que se ha hecho clic
        var url = this.href;

        // Fade out de los elementos principales y redireccionamiento después de la animación
        $('main, footer').fadeOut(400, function() {
            window.location.href = url;
        });
    });
});
