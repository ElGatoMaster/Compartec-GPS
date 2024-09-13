<?php
// Verificar si se ha iniciado sesión
$conexion = include('../../php/conexion.php');
require '../../php/sesion.php';
session_start();

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se están editando el nombre
    if (isset($_POST['nuevoNombre']) && !empty($_POST['nuevoNombre']) && isset($_POST['nuevoApellido']) && !empty($_POST['nuevoApellido'])) {
        // Procesar la edición del nombre y apellido
        $nuevoNombre = $_POST['nuevoNombre'];
        $nuevoApellido = $_POST['nuevoApellido'];
        // Actualizar el nombre y apellido en la base de datos
        $idUsuario = $_SESSION['idU'];
        $sql = "UPDATE usuario SET nom_Us = '$nuevoNombre', apell_Us = '$nuevoApellido' WHERE idUsuario = $idUsuario";
        // Ejecutar la consulta SQL
        if (mysqli_query($conexion, $sql)) {
            // Redirigir a la página de perfil o a donde desees
            header("Location: infoperfil.php");
            ReloadLogin($conexion,$idUsuario);
            exit;
        } else {
            echo "Error al actualizar el nombre y apellido: " . mysqli_error($conexion);
        }
    }

    
    // Verificar si se están editando la carrera
    if (isset($_POST['nuevaCarrera']) && !empty($_POST['nuevaCarrera'])) {
        // Procesar la edición de la carrera
        $nuevaCarrera = $_POST['nuevaCarrera'];
        // Actualizar la carrera en la base de datos
        $idUsuario = $_SESSION['idU']; // Asegúrate de que $_SESSION['idUsuario'] tenga el ID del usuario actual
        $sql = "UPDATE usuario SET carrera_Us = '$nuevaCarrera' WHERE idUsuario = $idUsuario";
        // Ejecutar la consulta SQL
        if (mysqli_query($conexion, $sql)) {
            // Redirigir a la página de perfil o a donde desees
            ReloadLogin($conexion,$idUsuario);
            header("Location: infoperfil.php");
            exit;
        } else {
            echo "Error al actualizar la carrera: " . mysqli_error($conexion);
        }
    }
}
// Si se llega aquí, significa que se intentó acceder a este script directamente sin enviar el formulario
// Deberías redirigir a alguna página apropiada o mostrar un mensaje de error
header("Location: infoperfil.php");
exit;
?>
