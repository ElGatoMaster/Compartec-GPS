$(document).ready(function(){
    const estrellas = document.querySelectorAll('.bi-star-fill');

    var calificacion = 0;

    var times = 0;
    
    estrellas.forEach(function(estrella,index) {
        estrella.addEventListener('click',function miFuncion(){
            for(let i =0; i<=index; i++){
                estrellas[i].classList.add('checado');
                estrellas[i].classList.remove('estrella');
            }
            for(let i=index+1; i<=estrellas.length-1; i++){
                estrellas[i].classList.remove('checado');
                estrellas[i].classList.remove('estrella');
            }
    
            calificacion = parseInt($(this).data('rating'));
    
            $.ajax({
                URL:"publicacion_detalle.php",
                method:"POST",
                dataType: 'json',
                data:{guardar:1,calificacion:calificacion},
                success:function(r){
                    location.reload();
                    localStorage.setItem('uID', r.id);
                    location.reload();
                    
                }
            });
               
            
        });//estrella
    });//Estrellas


});//document

