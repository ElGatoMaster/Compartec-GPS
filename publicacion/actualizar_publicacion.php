<?php
$conexion = include('../php/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idPublicacion = $_POST['idPub'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $carrera = $_POST['cbx_carrera'];
    $materia = $_POST['cbx_materia'];
    $tipo = $_POST['tipo'];
    // Consulta SQL para actualizar la publicación
    $consulta = "UPDATE publicacion SET 
                 titulo_Pub = '$titulo', 
                 descrip_Pub = '$descripcion', 
                 carrera_Pub = '$carrera', 
                 materia_Pub = '$materia', 
                 tipo_pub = '$tipo'
                 WHERE idPub = $idPublicacion";

    if (mysqli_query($conexion, $consulta)) {
        header("Location: ../administracion/Perfil/infoperfil.php?mensaje=Publicación actualizada exitosamente");
        exit();
    } else {
        echo "Error al actualizar la publicación: " . mysqli_error($conexion);
        exit();
    }
}

// Cierra la conexión a la base de datos
mysqli_close($conexion);
?>
