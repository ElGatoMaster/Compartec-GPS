<?php
include('../php/functions.php');
$link = include('../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

$input = $_GET['input'];
$carrera = $_GET['carrera'];

$consulta = "SELECT nomMateria FROM materia WHERE nomCarrera = '$carrera' 
AND nomMateria LIKE '%$input%' AND estadoMateria = 1";
$resultado = mysqli_query($link, $consulta);

// Construir la lista de sugerencias
if (mysqli_num_rows($resultado) > 0) {
  while ($fila = mysqli_fetch_assoc($resultado)) {
    echo '<li>' . $fila['nomMateria'] . '</li>';
  }
} else {
  echo '<li>No hay sugerencias</li>';
}

mysqli_close($link); // Cierra la conexión después de realizar la consulta
?>
