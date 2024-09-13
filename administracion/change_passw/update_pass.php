<?php
session_start();
$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión


$newPass = $_POST['newPassword'];
if(isset($_SESSION['correo_ch'])){
    $correo = $_SESSION['correo_ch'];
}else{
    $correo = $_POST['email'];
}

$newPass = password_hash($newPass, PASSWORD_BCRYPT);

// Preparar la consulta
$sql = "UPDATE usuario SET contra_Us = '$newPass' WHERE correo_Us = '$correo';";
$registros = mysqli_query($link, $sql); // Utiliza la conexión obtenida desde el archivo de conexión

$_SESSION["alert_message"] = "Contraseña actualizada exitosamente, por favor inicie sesion para continuar";
header('Location: ../../index.php');

if (!$registros) {
    die('Error en la consulta: ' . mysqli_error($link));
}

mysqli_close($link);
?>