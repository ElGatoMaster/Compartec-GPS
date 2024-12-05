<?php
$link = include('conexion.php'); // Incluye el archivo de conexión y obtén la conexión
 
//consultas para el combo box

$nomCarrera = $_POST ['nomCarrera'];

$consultaM = "SELECT nomMateria FROM materia WHERE 
       nomCarrera = '$nomCarrera' AND estadoMateria = 1";

$resM = mysqli_query($link,$consultaM); // Utiliza la conexión obtenida desde el archivo de conexión

// Verifica si la consulta se ejecutó correctamente
if (!$resM) {
  die('Error en la consulta: ' . mysqli_error($link));
}


WHILE($ROWM = $resM ->fetch_assoc() )
{
    $html.= "<option value='".$ROWM['nomMateria']."'>".$ROWM['nomMateria']."</option>";
}

echo $html;

?>