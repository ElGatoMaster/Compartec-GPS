<?php
$link = mysqli_connect("localhost", "root", "root", "bibliotec");

if (!$link) {
    die("Error en la conexión: " . mysqli_connect_error());
}else{
    
}

function cerrarconexion(){
    mysqli_close($GLOBALS['link']); //Esta funcion permite cerrar la conexion una vez que se mande a llamar
}
// Retornar la conexión para que esté disponible en otros archivos
return $link;

?>