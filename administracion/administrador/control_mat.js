$(document).ready(function(){
    var regMat;

    $('#modal_editar').on('show.bs.modal',function(event){
        var btn = $(event.relatedTarget);
        var jsonRow = btn.data('materia');
        regMat =  JSON.parse(JSON.stringify(jsonRow));

        //console.log(regMat);
        $(this).find('.modal-title').text('Editar Materia '+regMat['nomMateria']);
        $(this).find('.modal-body input#textMateriaEd').val(regMat['nomMateria']);
        $(this).find('.modal-body select#selectCarreraEd').val(regMat['nomCarrera']);

        if(regMat['estadoMateria'] == 1){
            $(this).find('.modal-body input#checkEstado').prop('checked',true);
        }else{
            $(this).find('.modal-body input#checkEstado').prop('checked',false);
        }
    });//--Abrir el modal de editar

    $('#modal_eliminar').on('show.bs.modal',function(event){
        var btn = $(event.relatedTarget);
        regMat = JSON.parse(JSON.stringify(btn.data('eliminar')));

        //console.log(regMat);
        $(this).find('.modal-title').text('Eliminar Materia '+regMat['nomMateria']);
        $(this).find('.modal-body b#elMateria').text(regMat.nomMateria);
    });//---Abrir el modal de eliminar

    $('#agregar_materia').click(function(){
        var nomMateria = $('#textMateria').val();
        var nomCarrera = $('.form-select').val();
        var estadoMateria = $('#checkEstado').prop('checked');
        if(estadoMateria){
            estadoMateria = 1;
        }else{
            estadoMateria = 0;
        }
        $.ajax({
            url: 'admin_materias.php',
            method: 'POST',
            data: {
                pls:1,
                nuevaMateria:nomMateria,
                nuevaCarrera:nomCarrera,
                estadoMateria:estadoMateria
            },
            success:function(r){
                alert('Se agregó la materia');
                $('#modal_agregar').modal('hide');
                location.reload();
                location.reload();
            }
        });
    });//AGREGAR MATWERIA

    $('#modificar_materia').click(function(){
        var nomMateria = $('#textMateriaEd').val();
        var nomCarrera = $('#selectCarreraEd').val();
        var estadoMateria = $('#checkEstado').prop('checked');

        if(estadoMateria){
            estadoMateria = 1;
        }else{
            estadoMateria = 0;
        }

        $.ajax({
            url: 'admin_materias.php',
            method: 'POST',
            data:{
                original:regMat,
                nomMateria:nomMateria,
                nomCarrera:nomCarrera,
                estadoMateria:estadoMateria
            },
            success:function(r){
                alert("Materia actualizada");
                $('#modal_editar').modal('hide');
                location.reload();
                location.reload();
            }
        });
    }); //editar materia

    $('#eliminar_materia').click(function(){
        $.ajax({
            URL: 'admin_materias.php',
            method: 'POST',
            data: {
                eliminar:1,
                materia:regMat
            },
            success:function(r){
                $('#modal_eliminar').modal('hide');
                alert("Materia eliminada");
                location.reload();
                location.reload();
            }
        });
    });//ELIMINAR MATERIA
    
});//final del doc

