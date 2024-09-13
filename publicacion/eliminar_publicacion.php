<?php
// Incluye el archivo de conexión a la base de datos
$conexion = include('../php/conexion.php');

// Verifica si se ha enviado el ID de la publicación a eliminar
if (isset($_POST['idpub'])) {
    $idPublicacion = $_POST['idpub'];

    // Prepara la consulta SQL para obtener el nombre del archivo adjunto
    $consulta_nombre_archivo = "SELECT archivo_Pub FROM `publicacion` WHERE `idPub` = $idPublicacion";

    // Ejecuta la consulta para obtener el nombre del archivo adjunto
    $resultado_nombre_archivo = mysqli_query($conexion, $consulta_nombre_archivo);

    // Verifica si se obtuvo el nombre del archivo adjunto correctamente
    if (mysqli_num_rows($resultado_nombre_archivo) > 0) {
        $fila = mysqli_fetch_assoc($resultado_nombre_archivo);

            // Prepara la consulta SQL para eliminar la publicación
            $consulta = "UPDATE `publicacion` set estado_Pub = 2 WHERE `idPub` = $idPublicacion";

            // Ejecuta la consulta para eliminar la publicación
            if (mysqli_query($conexion, $consulta)) {
                // Redirige al usuario a la página de perfil con un mensaje de éxito
                header("Location: ../administracion/Perfil/infoperfil.php?mensaje=Publicación eliminada exitosamente");
                exit();
            } else {
                // Redirige al usuario a la página de perfil con un mensaje de error
                header("Location: ../administracion/Perfil/infoperfil.php?mensaje=Error al eliminar la publicación");
                exit();
            }
        
    } else {
        // Si no se encuentra el nombre del archivo adjunto en la base de datos, redirige con un mensaje de error
        header("Location: ../administracion/Perfil/infoperfil.php?mensaje=No se encontró el archivo adjunto");
        exit();
    }
} else {
    // Si no se ha enviado el ID de la publicación, redirige al usuario a la página de perfil
    header("Location: ../administracion/Perfil/infoperfil.php");
    exit();
}

// Cierra la conexión a la base de datos
mysqli_close($conexion);
?>

