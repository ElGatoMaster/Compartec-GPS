$(document).ready(function(){
    var idComentario = 0;
    //var contenido = "";
    //var conteoRep = 0;
    
    
    //const pub = document.querySelector('#reportarPub');//boton de reportar publicacion
    const com = document.querySelectorAll('#reportarCom');//boton de reportar en los comentarios
    
    com.forEach(function(coms){
        coms.addEventListener('click',function(){
            //contenido = "comentario";
            idComentario = parseInt($(this).data('comid'));
        });
    });
    
    
    $('#subir_reporte_p').click(function(){
        var motivo = $('.selepub').val();
        //console.log(motivo);
        if(motivo == "Seleccionar"){
            alert("Seleccione un motivo por favor");
            return false;
        }else{
            console.log(motivo);
            $.ajax({
                URL:"publicacion_detalle.php",
                method:"POST",
                data: {repub:1,motivo:motivo},
                success:function(r){
                    //localStorage.setItem('uID', r.id);                    
                    $('#modal_report_p').modal('hide');
                    alert("publicacion reportada ");
                }
            });
        }
    });
    
    $('#subir_reporte_c').click(function(){
        var motivo = $('.selecom').val();
        //console.log(motivo);
        //console.log(idComentario);
        if(motivo == "Seleccionar"){
            alert("Seleccione un motivo por favor");
            return false;
        }else{
            console.log(motivo);
            console.log(idComentario);
            $.ajax({
                URL:"publicacion_detalle.php",
                method:"POST",
                data:{repCom:1,motivo:motivo,idComentario:idComentario},
                success:function(r){
                    $('#modal_report_c').modal('hide');
                    alert("reportado con exito");
                }
            });
        }
    });

});