<?php
include('../php/conexion.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtener las credenciales del formulario
    $idPub = $_POST["id_publicacion"];
    $comentario = $_POST["comentario"];
    // Obtener los datos del usuario
    $idUser = $_SESSION['idU'];

    // Preparar la consulta
    $sql = "INSERT INTO comentario (idPub, idUsuario, text_Coment, fecha_Coment) VALUES ($idPub, $idUser, '$comentario', CURDATE())";
    $registros = mysqli_query($link, $sql); // Utiliza la conexión obtenida desde el archivo de conexión
    mysqli_close($link);  
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
