<?php
session_start();
$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

$tokenIn = $_POST['codigoIn'];//Token que ingresa en la ventana de verificacion

$correo = isset($_SESSION['enviarCorreo'])? $_SESSION['enviarCorreo'] : ''; //Se obtiene el correo de la ventana de registro para obtener el resto de datos y hacer la comparacion necesaria.

//se obtiene el token generado y guardado en la tabla
$tokenBD = mysqli_query($link,"SELECT `token` FROM `usuario_temp` WHERE `correo_Us` = '$correo' ORDER BY idUsuario DESC");

$tokenBD =  mysqli_fetch_array($tokenBD); //conversion de arreglo

if(strcmp($tokenBD[0],$tokenIn) !== 0){
    echo '<script>
        window.alert("El código ingresado no es correcto, intentelo de nuevo");
        window.history.back();
    </script>';
    exit;
}

//INSERTA LOS VALORES EN LA TABLA SI CORRESPONDEN LOS TOKENS O CLAVES.
$query = "INSERT INTO `usuario`( `nom_Us`, `apell_Us`, `carrera_Us`, `semestre_Us`, `correo_Us`, `contra_Us`) 
SELECT `nom_Us`, `apell_Us`, `carrera_Us`, `semestre_Us`, `correo_Us`, `contra_Us` 
FROM `usuario_temp` 
WHERE `correo_Us` = '$correo'";//VALUES ('$nombre','$apellidos','$carrera','$semestre','$correo','$contrasena')


$ejecutar = mysqli_query($link,$query);

//EJECUTA EL QUERY EN EL QUE SE GUARDAN LOS DATOS EN LA TABLA TEMPORAL ESPERANDO LA VALIDACION DEL CORREO
if($ejecutar){

    mysqli_query($link,"DELETE FROM `usuario_temp` WHERE `correo_Us` = '$correo'");

    echo '<script>
        window.alert("Se ha validado el correo exitosamente");
        window.location = "../../index.php";
    </script>';
}else{
    echo '<script>
        window.alert("Ha ocurrido un error, intentelo de nuevo");
        window.history.back();
    </script>';//window.location = "register.php";
}

unset($_SESSION['enviarCorreo']);
session_destroy();
mysqli_close($link);
?>