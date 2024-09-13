$(document).ready(function(){
    var idpub=0;
    
    const pubEli = document.querySelectorAll('#idEliminar');//boton de eliminar
    const pubEli2 = document.querySelectorAll('#idEliminar2');//boton de eliminar
    
    pubEli.forEach(function(pubs){
        pubs.addEventListener('click',function(){
            //contenido = "comentario";
            idpub = parseInt($(this).data('idpub'));
            console.log(idpub);
        });
    });
    
    /*$("#idEliminar").click(function(){
        idpub = parseInt(("#idEliminar").data('idpub'));
        console.log(idpub);
    })*/
    
    
    $("#ConfirmarEliminar").click(function(){
        console.log(idpub);
        $.ajax({
            url:"../../publicacion/eliminar_publicacion.php",
            method:"POST",
            data:{Confirmar:1,idpub:idpub},
            success:function(){
                alert("Publicacion eliminada de manera exitosa");
                $('.modal').modal('hide');
            }
        })
    })
    
    
    //CREO QUE ESTO es del ADMIN

    pubEli2.forEach(function(pubs2){
        pubs2.addEventListener('click',function(){
            //contenido = "comentario";
            idpub = parseInt($(this).data('idpub'));
            console.log(idpub);
        });
    });

    /*$("#idEliminar2").click(function(){
        idpub = parseInt($(this).data('idpub'));
        console.log(idpub);
    })*/
    
    
    $("#ConfirmarEliminar2").click(function(){
        console.log(idpub);
        $.ajax({
            url:"../../publicacion/eliminar_publicacion.php",
            method:"POST",
            data:{Confirmar:1,idpub:idpub},
            success:function(){
                alert("Publicacion eliminada de manera exitosa");
                $('.modal').modal('hide');
            }
        })
    })
})

